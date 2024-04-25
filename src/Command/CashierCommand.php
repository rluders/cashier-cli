<?php

namespace App\Command;

use App\Entity\CartItem;
use App\Service\CartService;
use App\Service\CatalogService;
use NumberFormatter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

#[AsCommand(
    name: 'cashier',
    description: 'CLI Cashier Command',
    hidden: false,
)]
class CashierCommand extends Command
{
    protected const string MAIN_MENU = 'main_menu';
    protected const string CATALOG_MENU = 'catalog_menu';
    protected const string VIEW_CART_MENU = 'view_cart_menu';

    protected const string BACK_OPTION = 'B';
    protected const string QUIT_OPTION = 'Q';
    protected const string VIEW_CATALOG_OPTION = 'P';
    protected const string VIEW_CART_OPTION = 'C';

    protected bool $loop = true;
    protected string $currentState;
    protected string $previousState;

    public function __construct(
        protected CatalogService $catalogService,
        protected CartService $cartService,
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        // Default state
        $this->currentState = self::MAIN_MENU;
    }

    protected function setCurrentState(string $state): void
    {
        $this->previousState = $this->currentState;
        $this->currentState = $state;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        while ($this->loop) {
            $this->clearScreen($output); // Clear screen

            switch ($this->currentState) {
                case self::MAIN_MENU:
                    $this->showMainMenu($input, $output);
                    break;
                case self::CATALOG_MENU:
                    $this->showCatalogMenu($input, $output);
                    break;
                case self::VIEW_CART_MENU:
                    $this->showViewCartMenu($input, $output);
                    break;
            }
        }

        $output->writeln('Quitting. Cart cleared.');
        return Command::SUCCESS;
    }

    protected function showMainMenu(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln([
            '=====================',
            ' Welcome to the Shop ',
            '=====================',
            '',
        ]);

        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'What would you like to do?',
            [
                self::VIEW_CATALOG_OPTION => 'View Catalog',
                self::VIEW_CART_OPTION => 'View Cart',
                self::QUIT_OPTION => 'Quit'
            ],
            self::VIEW_CATALOG_OPTION
        );
        $question->setNormalizer(fn(string $value): string => $value ? strtoupper(trim($value)) : '');

        $option = $helper->ask($input, $output, $question);

        switch ($option) {
            case self::VIEW_CATALOG_OPTION:
                $this->setCurrentState(self::CATALOG_MENU);
                break;
            case self::VIEW_CART_OPTION:
                $this->setCurrentState(self::VIEW_CART_MENU);
                break;
            case self::QUIT_OPTION:
                $this->loop = false;
                return;
        }
    }

    protected function showCatalogMenu(InputInterface $input, OutputInterface $output): void
    {
        $helper = $this->getHelper('question');

        // Show the catalog and options
        while (true) {
            $this->clearScreen($output); // Clear screen

            $this->renderCatalogTable($output);

            // Get all product names from the catalog service
            $productNames = array_map(fn ($item) => $item->getName(), $this->catalogService->getAllProducts());

            // Add additional options for viewing cart and quitting
            $options = array_merge($productNames, [self::VIEW_CART_OPTION => 'View Cart', self::QUIT_OPTION => 'Quit']);

            $question = new ChoiceQuestion(
                'Please select a product or action:',
                $options,
                self::VIEW_CART_OPTION
            );
            $question->setNormalizer(fn(string $value): string => $value ? strtoupper(trim($value)) : '');

            $option = $helper->ask($input, $output, $question);

            if ($option === self::VIEW_CART_OPTION) {
                $this->setCurrentState(self::VIEW_CART_MENU);
                return;
            }

            if ($option === self::QUIT_OPTION) {
                $this->loop = false;
                return;
            }

            // Add product to cart
            $productCode = $option; // just to make it easier to read
            $product = $this->catalogService->getProduct($productCode);
            if ($product === null) {
                // It should never happen, but...
                $output->writeln('Product not found.');

                $this->pressEnterToContinue($input, $output);
                continue;
            }

            // How many items?
            $question = new Question('Enter the quantity: ');
            $quantity = $helper->ask($input, $output, $question);
            $quantity = intval($quantity);

            if ($quantity < 0) {
                $output->writeln('Invalid quantity. Please enter a positive number.');

                $this->pressEnterToContinue($input, $output);
                continue;
            }

            // Check if the product is already in the cart
            $existingCartItem = $this->cartService->getItemByProductCode($product->getCode());
            if ($existingCartItem) {
                if ($quantity === 0) {
                    $this->cartService->removeItem($existingCartItem);

                    $output->writeln(['', '>>> Item removed from cart.', '']);

                    $this->pressEnterToContinue($input, $output);
                    continue;
                }

                // If the product is already in the cart, update the quantity.
                $existingCartItem->setQuantity($quantity);

                $output->writeln(['', '>>> Item quantity updated.', '']);

                $this->pressEnterToContinue($input, $output);
                continue;
            }

            if ($quantity == 0) {
                $output->writeln(['', 'Cannot add item within given quantity to the cart.', '']);

                $this->pressEnterToContinue($input, $output);
                continue;
            }

            // Otherwise, add a new item to the cart
            $this->cartService->addItem(new CartItem($product, $quantity));

            $output->writeln(['', '>>> Product added to cart.', '']);

            $this->pressEnterToContinue($input, $output);
        }
    }

    protected function showViewCartMenu(InputInterface $input, OutputInterface $output): void
    {
        $this->clearScreen($output); // Clear screen

        $this->renderCartTable($output);

        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'What would you like to do?',
            [self::BACK_OPTION => 'Back', self::QUIT_OPTION => 'Quit'],
            self::BACK_OPTION
        );
        $question->setNormalizer(fn(string $value): string => $value ? strtoupper(trim($value)) : '');

        $option = $helper->ask($input, $output, $question);

        switch ($option) {
            case self::BACK_OPTION:
                $this->setCurrentState($this->previousState);
                break;
            case self::QUIT_OPTION:
                $this->loop = false;
                return;
        }
    }

    protected function renderCartTable(OutputInterface $output): void
    {
        $cart = $this->cartService->getCart();
        if ($cart->isEmpty()) {
            $output->writeln(['', 'Empty cart', '']);
            return;
        }

        $fmt = new NumberFormatter('es_ES', NumberFormatter::CURRENCY);

        $rows = [];
        foreach ($cart->getItems() as $item) {
            $fullPrice = $item->getFullPrice();
            $discountAmount = $fullPrice - $item->getPrice();

            $rows[] = [
                $item->getProduct()->getCode(),
                $item->getProduct()->getName(),
                $item->getQuantity(),
                numfmt_format_currency($fmt, $fullPrice, 'EUR'),
                numfmt_format_currency($fmt, $discountAmount, 'EUR'),
                numfmt_format_currency($fmt, $item->getPrice(), 'EUR'),
            ];
        }

        $table = new Table($output);
        $table
            ->setHeaders(['Code', 'Name', 'Quantity', 'Price', 'Discount', 'Final Price'])
            ->setRows($rows);

        $table->render();
    }

    protected function renderCatalogTable(OutputInterface $output): void
    {
        $fmt = new NumberFormatter('es_ES', NumberFormatter::CURRENCY);

        $rows = [];
        foreach ($this->catalogService->getAllProducts() as $product) {
            $rows[] = [
                $product->getCode(),
                $product->getName(),
                numfmt_format_currency($fmt, $product->getPrice(), 'EUR'),
            ];
        }

        $table = new Table($output);
        $table
            ->setHeaders(['Code', 'Name', 'Price'])
            ->setRows($rows);

        $table->render();
    }

    protected function clearScreen(OutputInterface $output): void
    {
        $output->write("\033\143");
    }

    public function pressEnterToContinue(InputInterface $input, OutputInterface $output): void
    {
        $helper = $this->getHelper('question');

        $question = new ConfirmationQuestion(
            '------ Press ENTER to continue ------',
            true,
        );
        $helper->ask($input, $output, $question);
    }
}

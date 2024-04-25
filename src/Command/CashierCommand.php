<?php

namespace App\Command;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Service\DiscountService;
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
    protected const MAIN_MENU = 'main_menu';
    protected const CATALOG_MENU = 'catalog_menu';
    protected const VIEW_CART_MENU = 'view_cart_menu';

    protected const BACK_OPTION = 'B';
    protected const QUIT_OPTION = 'Q';
    protected const VIEW_CATALOG_OPTION = 'P';
    protected const VIEW_CART_OPTION = 'C';


    protected bool $loop = true;
    protected string $currentState;
    protected string $previousState;

    protected array $catalog = [];
    protected Cart $cart;


    public function __construct(
        protected DiscountService $discountService
    ) {
        parent::__construct();

        // Start empty cart
        $this->cart = new Cart();
    }

    public function configure(): void
    {
        // Just pushing some items to the "catalog". I could totally get it from SQLite
        // do some migration, seeder, repository, or whatever... but why?
        $this->catalog['GR1'] = new Product('GR1', 'Green Tea', 3.11);
        $this->catalog['CF1'] = new Product('CF1', 'Coffee', 11.23);
        $this->catalog['SR1'] = new Product('SR1', 'Strawberries', 5.00);

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

            $question = new ChoiceQuestion(
                'Please select a product or action:',
                array_merge(array_map(fn ($item) => $item->getName(), $this->catalog), [self::VIEW_CART_OPTION => 'View Cart', self::QUIT_OPTION => 'Quit']),
                self::VIEW_CART_OPTION
            );
            $question->setNormalizer(fn(string $value): string => $value ? strtoupper(trim($value)) : '');

            $option = $helper->ask($input, $output, $question);

            if ($option === self::VIEW_CART_OPTION) {
                $this->setCurrentState(self::VIEW_CART_MENU);
                return;
            } elseif ($option === self::QUIT_OPTION) {
                $this->loop = false;
                return;
            } else {
                // Add product to cart
                $product = $this->catalog[$option];

                // How many items?
                $question = new Question('Enter the quantity: ');
                $quantity = $helper->ask($input, $output, $question);
                $quantity = intval($quantity);

                if ($quantity < 0) {
                    $output->writeln('Invalid quantity. Please enter a positive number.');
                } else {
                    // Check if the product is already in the cart
                    $existingCartItem = $this->cart->findCartItemByProductCode($option);
                    if ($existingCartItem) {
                        // If the product is already in the cart, update the quantity
                        $existingCartItem->setQuantity($existingCartItem->getQuantity() + $quantity);
                    } else {
                        // Otherwise, add a new item to the cart
                        $this->cart->addItem(new CartItem($product, $quantity));
                    }

                    $output->writeln(['', '>>> Product added to cart.', '']);

                    $this->pressEnterToContinue($input, $output);
                }
            }
        }
    }

    protected function showViewCartMenu(InputInterface $input, OutputInterface $output): void
    {
        $this->clearScreen($output); // Clear screen

        $this->discountService->executeRules($this->cart);
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
        if ($this->cart->isEmpty()) {
            $output->writeln(['', 'Empty cart', '']);
            return;
        }

        $fmt = new NumberFormatter('es_ES', NumberFormatter::CURRENCY);

        $rows = [];
        foreach ($this->cart->getItems() as $item) {
            $rows[] = [
                $item->getProduct()->getCode(),
                $item->getProduct()->getName(),
                $item->getQuantity(),
                numfmt_format_currency($fmt, $item->getPrice(), 'EUR'),
            ];
        }

        $table = new Table($output);
        $table
            ->setHeaders(['Code', 'Name', 'Quantity', 'Price'])
            ->setRows($rows);

        $table->render();
    }

    protected function renderCatalogTable(OutputInterface $output): void
    {
        $fmt = new NumberFormatter('es_ES', NumberFormatter::CURRENCY);

        $rows = [];
        foreach ($this->catalog as $product) {
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

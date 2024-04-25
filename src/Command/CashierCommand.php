<?php

namespace App\Command;

use App\Entity\Cart;
use App\Entity\Product;
use App\Service\DiscountService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
    }

    protected function showCatalogMenu(InputInterface $input, OutputInterface $output): void
    {
        throw \Exception('Needs to be implemented');
    }

    protected function showViewCartMenu(InputInterface $input, OutputInterface $output): void
    {
        throw \Exception('Needs to be implemented');
    }
}

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
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Quitting. Cart cleared.');
        return Command::SUCCESS;
    }
}

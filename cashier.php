#!/usr/bin/env php
<?php

/**
 * This a very basic CLI application with one single command. For these reasons, I'll not
 * use the Symfony Skeleton, and push a bunch of unnecessary dependencies.
 */

require __DIR__ . '/vendor/autoload.php';

use App\Command\CashierCommand;
use App\Rule\BulkDiscountRule;
use App\Rule\BuyOneGetOneFreeRule;
use App\Service\CartService;
use App\Service\CatalogService;
use App\Service\DiscountService;
use Symfony\Component\Console\Application;

$app = new Application("cashier", "1.0.0");

// Creates the DiscountService
$discountService = new DiscountService();
// Adds the Discount Rules to it, you can create your own just by 
// implement the provided interface and register it here.
$discountService->addRules(
    new BuyOneGetOneFreeRule(productCode: 'GR1'),
    new BulkDiscountRule(productCode: 'SR1', minQuantity: 3, discountedPrice: 4.50),
    new BulkDiscountRule(productCode: 'CF1', minQuantity: 3, discountedPrice: (2 / 3) * 11.23),
);

// Creates the CatalogService
$catalogService = new CatalogService();
// Creates the CartService
$cartService = new CartService($discountService);

// Creates the command, adds to the app and set it as default.
$command = new CashierCommand($catalogService, $cartService);

// Register the command to the application
$app->add($command);
$app->setDefaultCommand($command->getName(), true);

try {
    $app->run();
} catch (\Exception $e) {
    // Really basic exception handling
    echo 'Application error: '. $e->getMessage();
}

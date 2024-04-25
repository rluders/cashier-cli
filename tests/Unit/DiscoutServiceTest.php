<?php

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Rule\BulkDiscountRule;
use App\Rule\BuyOneGetOneFreeRule;
use App\Service\DiscountService;

it('should charge 2 Green Tea', function () {
    $cart = new Cart();
    $cart->addItem(new CartItem(new Product('GR1', 'Green Tea', 3.11), 2));

    $service = new DiscountService();
    $service->addRules(new BuyOneGetOneFreeRule('GR1'));

    $service->executeRules($cart);

    expect($cart->getTotalPrice())->toBe(3.11);
});

it('should charge 3 Strawberries and 1 Green Tea', function () {
    $cart = new Cart();
    $cart->addItem(new CartItem(new Product('SR1', 'Strawberries', 5.00), 3));
    $cart->addItem(new CartItem(new Product('GR1', 'Green Tea', 3.11), 1));

    $service = new DiscountService();
    $service->addRules(
        new BuyOneGetOneFreeRule('GR1'),
        new BulkDiscountRule('SR1', 3, 4.50)
    );

    $service->executeRules($cart);

    expect($cart->getTotalPrice())->toBe(16.61);
});

it('should charge 1 Green Tea, 3 Coffees, and 1 Strawberry', function () {
    $cart = new Cart();
    $cart->addItem(new CartItem(new Product('GR1', 'Green Tea', 3.11), 1));
    $cart->addItem(new CartItem(new Product('CF1', 'Coffee', 11.23), 3));
    $cart->addItem(new CartItem(new Product('SR1', 'Strawberries', 5.00), 1));

    $service = new DiscountService();
    $service->addRules(
        new BuyOneGetOneFreeRule('GR1'),
        new BulkDiscountRule('SR1', 3, 4.50),
        new BulkDiscountRule('CF1', 3, (2 / 3) * 11.23),
    );

    $service->executeRules($cart);

    expect($cart->getTotalPrice())->toBe(30.57);
});

it('should have an empty Cart', function () {
    $cart = new Cart();

    $service = new DiscountService();
    $service->addRules(
        new BuyOneGetOneFreeRule('GR1'),
        new BulkDiscountRule('SR1', 3, 4.50),
        new BulkDiscountRule('CF1', 3, (2 / 3) * 11.23),
    );

    $service->executeRules($cart);

    expect($cart->getTotalPrice())->toBe(0.0);
});


it('should have no discount rules registered', function () {
    $cart = new Cart();
    $cart->addItem(new CartItem(new Product('CF1', 'Coffee', 11.23), 1));
    $cart->addItem(new CartItem(new Product('SR1', 'Strawberries', 5.00), 2));

    $service = new DiscountService();

    $service->executeRules($cart);

    expect($cart->getTotalPrice())->toBe(21.23);
});

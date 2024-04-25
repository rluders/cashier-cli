<?php

use App\Entity\CartItem;
use App\Entity\Product;
use App\Service\CartService;
use App\Service\DiscountService;

it('should add an item to the cart', function () {
    $cartItem = new CartItem(new Product('GR1', 'Green Tea', 3.11), 2);
    $cartService = new CartService(new DiscountService());

    $cartService->addItem($cartItem);

    expect($cartService->getCart()->getItems())->toContain($cartItem);
});

it('should not add a duplicate item to the cart', function () {
    $product = new Product('CF1', 'Coffee', 11.23);
    $cartService = new CartService(new DiscountService());

    $cartService->addItem(new CartItem($product, 1));
    $cartService->addItem(new CartItem($product, 1));

    expect(count($cartService->getCart()->getItems()))->toBe(1);
});

it('should remove an item from the cart', function () {
    $product = new Product('SR1', 'Strawberries', 5.00);
    $cartService = new CartService(new DiscountService());
    $cartItem = new CartItem($product, 2);

    $cartService->addItem($cartItem);
    $cartService->removeItem($cartItem);

    expect($cartService->getCart()->getItems())->not->toContain($cartItem);
});

it('should return the correct cart item by product code', function () {
    $cartService = new CartService(new DiscountService());
    $product = new Product('GR1', 'Green Tea', 3.11);
    $cartItem = new CartItem($product, 2);

    $cartService->addItem($cartItem);
    $foundCartItem = $cartService->getItemByProductCode('GR1');

    expect($foundCartItem)->toBe($cartItem);
});

it('should return null when no cart item found by product code', function () {
    $cartService = new CartService(new DiscountService());

    $foundCartItem = $cartService->getItemByProductCode('CF1');

    expect($foundCartItem)->toBeNull();
});

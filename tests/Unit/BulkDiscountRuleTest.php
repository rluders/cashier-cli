<?php

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Rule\BulkDiscountRule;

it('should apply bulk discount correctly when quantity is equal to or greater than minQuantity', function () {
    $product = new Product('P1', 'Product 1', 10.0);
    $cartItem = new CartItem($product, 5);
    $cart = new Cart();
    $cart->addItem($cartItem);

    $rule = new BulkDiscountRule('P1', 3, 8.0);
    $rule->execute($cart);

    expect($cartItem->getPrice())->toBe(40.0); // 5 * 8.0
});

it('should not apply bulk discount when quantity is less than minQuantity', function () {
    $product = new Product('P2', 'Product 2', 10.0);
    $cartItem = new CartItem($product, 2);
    $cart = new Cart();
    $cart->addItem($cartItem);

    $rule = new BulkDiscountRule('P2', 3, 8.0);
    $rule->execute($cart);

    expect($cartItem->getPrice())->toBe(20.0); // 2 * 10.0
});

it('should not apply bulk discount when product does not match', function () {
    $product = new Product('P3', 'Product 3', 10.0);
    $cartItem = new CartItem($product, 5);
    $cart = new Cart();
    $cart->addItem($cartItem);

    $rule = new BulkDiscountRule('P2', 3, 8.0);
    $rule->execute($cart);

    expect($cartItem->getPrice())->toBe(50.0); // 5 * 10.0
});

it('should not apply bulk discount when cart is empty', function () {
    $cart = new Cart();

    $rule = new BulkDiscountRule('P1', 3, 8.0);
    $rule->execute($cart);

    expect($cart->getTotalPrice())->toBe(0.0);
});

<?php

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Rule\BuyOneGetOneFreeRule;

it('should apply buy one get one free discount correctly', function () {
    $product = new Product('P1', 'Product 1', 10.0);
    $cartItem = new CartItem($product, 5);
    $cart = new Cart();
    $cart->addItem($cartItem);

    $rule = new BuyOneGetOneFreeRule('P1');
    $rule->execute($cart);

    expect($cartItem->getPrice())->toBe(30.0); // 3 * 10.0
});

it('should not apply buy one get one free discount when quantity is 1', function () {
    $product = new Product('P2', 'Product 2', 10.0);
    $cartItem = new CartItem($product, 1);
    $cart = new Cart();
    $cart->addItem($cartItem);

    $rule = new BuyOneGetOneFreeRule('P2');
    $rule->execute($cart);

    expect($cartItem->getPrice())->toBe(10.0); // 1 * 10.0
});

it('should not apply buy one get one free discount when product does not match', function () {
    $product = new Product('P3', 'Product 3', 10.0);
    $cartItem = new CartItem($product, 5);
    $cart = new Cart();
    $cart->addItem($cartItem);

    $rule = new BuyOneGetOneFreeRule('P1');
    $rule->execute($cart);

    expect($cartItem->getPrice())->toBe(50.0); // 5 * 10.0
});

it('should not apply buy one get one free discount when cart is empty', function () {
    $cart = new Cart();

    $rule = new BuyOneGetOneFreeRule('P1');
    $rule->execute($cart);

    expect($cart->getTotalPrice())->toBe(0.0);
});

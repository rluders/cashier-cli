<?php

namespace App\Rule;

use App\Entity\Cart;

/**
 * Class BuyOneGetOneFreeRule
 *
 * Represents a discount rule that applies a "buy one, get one free" offer to a specific product in a cart.
 */
class BuyOneGetOneFreeRule implements DiscountRuleInterface
{
    /**
     * BuyOneGetOneFreeRule constructor.
     *
     * @param string $productCode The product code to which the "buy one, get one free" offer applies.
     */
    public function __construct(
        readonly protected string $productCode
    ) {}

    /**
     * Applies the "buy one, get one free" offer to the items in the cart.
     *
     * @param Cart $cart The cart to which the discount rule is applied.
     * @return void
     */
    public function execute(Cart $cart): void
    {
        foreach ($cart->getItems() as $item) {
            if ($item->getProduct()->getCode() === $this->productCode) {
                $quantity = $item->getQuantity() - intdiv($item->getQuantity(), 2);
                $item->setPrice($item->getProduct()->getPrice() * $quantity);
            }
        }
    }
}

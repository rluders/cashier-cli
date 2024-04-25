<?php

namespace App\Rule;

use App\Entity\Cart;

class BuyOneGetOneFreeRule implements DiscountRuleInterface
{
    public function __construct(
        readonly protected string $productCode
    ) {
    }

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

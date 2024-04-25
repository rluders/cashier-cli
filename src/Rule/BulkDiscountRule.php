<?php

namespace App\Rule;

use App\Entity\Cart;

class BulkDiscountRule implements DiscountRuleInterface
{
    public function __construct(
        readonly private string $productCode,
        readonly private float $minQuantity,
        readonly private float $discountedPrice,
    ) {
    }

    public function execute(Cart $cart): void
    {
        foreach ($cart->getItems() as $item) {
            if ($item->getProduct()->getCode() === $this->productCode) {
                if ($item->getQuantity() < $this->minQuantity) {
                    continue;
                }
                $item->setPrice($this->discountedPrice * $item->getQuantity());
            }
        }
    }
}
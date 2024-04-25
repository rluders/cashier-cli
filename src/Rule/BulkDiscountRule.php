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
        throw new \Exception('Needs to be implemented');
    }
}
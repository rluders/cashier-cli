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
        throw new \Exception('Needs to be implemented');
    }
}

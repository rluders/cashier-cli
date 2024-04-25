<?php

namespace App\Rule;

use App\Entity\Cart;

interface DiscountRuleInterface
{
    public function execute(Cart $cart): void;
}

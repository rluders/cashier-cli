<?php

namespace App\Service;

use App\Entity\Cart;
use App\Rule\DiscountRuleInterface;

class DiscountService
{
    private array $rules = [];

    public function addRules(DiscountRuleInterface ...$rules): void
    {
        throw new \Exception("Needs to be implemented");
    }

    public function executeRules(Cart $cart): void
    {
        throw new \Exception("Needs to be implemented");
    }
}

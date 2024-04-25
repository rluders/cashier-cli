<?php

namespace App\Service;

use App\Entity\Cart;
use App\Rule\DiscountRuleInterface;

class DiscountService
{
    private array $rules = [];

    public function addRules(DiscountRuleInterface ...$rules): void
    {
        foreach ($rules as $rule) {
            $this->rules[] = $rule;
        }
    }

    public function executeRules(Cart $cart): void
    {
        foreach ($this->rules as $rule) {
            $rule->execute($cart);
        }
    }
}

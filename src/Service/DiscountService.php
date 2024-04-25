<?php

namespace App\Service;

use App\Entity\Cart;
use App\Rule\DiscountRuleInterface;

/**
 * Class DiscountService
 *
 * Represents a service for applying discount rules to a cart.
 */
class DiscountService
{
    /**
     * @var DiscountRuleInterface[] The array of discount rules to be applied.
     */
    private array $rules = [];

    /**
     * Adds one or more discount rules to the service.
     *
     * @param DiscountRuleInterface ...$rules The discount rules to add.
     * @return void
     */
    public function addRules(DiscountRuleInterface ...$rules): void
    {
        foreach ($rules as $rule) {
            $this->rules[] = $rule;
        }
    }

    /**
     * Executes all added discount rules on the provided cart.
     *
     * @param Cart $cart The cart to which the discount rules are applied.
     * @return void
     */
    public function executeRules(Cart $cart): void
    {
        foreach ($this->rules as $rule) {
            $rule->execute($cart);
        }
    }
}

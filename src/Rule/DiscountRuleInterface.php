<?php

namespace App\Rule;

use App\Entity\Cart;

/**
 * Interface DiscountRuleInterface
 *
 * This interface defines the contract for discount rules that can be applied to a shopping cart.
 * Implementing classes must provide an implementation for the execute method.
 */
interface DiscountRuleInterface
{
    /**
     * Executes the discount rule on the given shopping cart.
     *
     * @param Cart $cart The shopping cart to apply the discount rule to.
     *
     * @return void
     */
    public function execute(Cart $cart): void;
}

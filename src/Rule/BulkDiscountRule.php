<?php

namespace App\Rule;

use App\Entity\Cart;

/**
 * Class BulkDiscountRule
 *
 * Represents a discount rule that applies bulk discounts to specific products in a cart.
 */
class BulkDiscountRule implements DiscountRuleInterface
{
    /**
     * BulkDiscountRule constructor.
     *
     * @param string $productCode The product code to which the bulk discount applies.
     * @param float $minQuantity The minimum quantity required for the bulk discount to be applied.
     * @param float $discountedPrice The discounted price to be set for each item meeting the minimum quantity requirement.
     */
    public function __construct(
        readonly private string $productCode,
        readonly private float $minQuantity,
        readonly private float $discountedPrice
    ) {}

    /**
     * Applies the bulk discount rule to the items in the cart.
     *
     * @param Cart $cart The cart to which the bulk discount rule is applied.
     * @return void
     */
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

<?php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\CartItem;

/**
 * Class CartService
 *
 * This service class provides functionality related to managing a shopping cart.
 */
class CartService
{
    /**
     * @var Cart The shopping cart object.
     */
    private Cart $cart;

    /**
     * CartService constructor.
     *
     * @param DiscountService $discountService The discount service used to apply discounts to the cart.
     */
    public function __construct(
        /** @var DiscountService The discount service used to apply discounts to the cart. **/
        protected DiscountService $discountService
    ) {
        // In this case we can only have one cart, and this is fine.
        $this->cart = new Cart();
    }

    /**
     * Retrieves the shopping cart.
     *
     * @return Cart The shopping cart object.
     */
    public function getCart(): Cart
    {
        // Execute discount rules before returning the cart
        $this->discountService->executeRules($this->cart);
        return $this->cart;
    }

    /**
     * Adds an item to the shopping cart.
     *
     * @param CartItem $cartItem The cart item to add.
     */
    public function addItem(CartItem $cartItem): void
    {
        // Check if the item already exists in the cart
        $existingItem = $this->getItemByProductCode($cartItem->getProduct()->getCode());
        if (!$existingItem) {
            $this->cart->addItem($cartItem);
        }
    }

    /**
     * Removes an item from the shopping cart.
     *
     * @param CartItem $item The cart item to remove.
     */
    public function removeItem(CartItem $item): void
    {
        $this->cart->removeItem($item);
    }

    /**
     * Finds a cart item in the cart by its product code.
     *
     * @param string $code The product code to search for.
     *
     * @return CartItem|null The cart item found, or null if not found.
     */
    public function getItemByProductCode(string $code): ?CartItem
    {
        return current(array_filter(
            $this->cart->getItems(),
            fn ($item) => $item->getProduct()->getCode() === $code
        )) ?: null;
    }
}

<?php

namespace App\Entity;

/**
 * Class Cart
 *
 * Represents a shopping cart containing items.
 */
class Cart
{
    /**
     * Cart constructor.
     *
     * @param array $items An array of CartItem objects representing the items in the cart.
     */
    public function __construct(
        /** @var array An array containing the items in the cart. **/
        protected array $items = []
    ) {}

    /**
     * Adds a CartItem to the cart.
     *
     * @param CartItem $item The CartItem to add to the cart.
     *
     * @return Cart Returns the updated Cart instance.
     */
    public function addItem(CartItem $item): self
    {
        $this->items[] = $item;
        return $this;
    }

    /**
     * Retrieves all items in the cart.
     *
     * @return array An array containing all CartItem objects in the cart.
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Calculates and returns the total price of all items in the cart.
     *
     * @return float The total price of all items in the cart.
     */
    public function getTotalPrice(): float
    {
        $totalPrice = 0;
        foreach ($this->items as $item) {
            $totalPrice += $item->getPrice();
        }
        return ceil($totalPrice * 100) / 100;
    }

    /**
     * Finds a CartItem in the cart by its product code.
     *
     * @param string $productCode The product code to search for.
     *
     * @return CartItem|null The CartItem found, or null if not found.
     */
    public function findCartItemByProductCode(string $productCode): ?CartItem
    {
        foreach ($this->items as $item) {
            if ($item->getProduct()->getCode() === $productCode) {
                return $item;
            }
        }
        return null;
    }

    /**
     * Checks if the cart is empty.
     *
     * @return bool True if the cart is empty, false otherwise.
     */
    public function isEmpty(): bool
    {
        return empty($this->items);
    }
}

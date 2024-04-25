<?php

namespace App\Entity;

/**
 * Class CartItem
 *
 * Represents an item in a shopping cart.
 */
class CartItem
{
    /**
     * @var float The price of this cart item.
     */
    protected float $price = 0;

    /**
     * CartItem constructor.
     *
     * @param Product $product The product associated with this cart item.
     * @param int $quantity The quantity of this cart item.
     */
    public function __construct(
        /** @var Product The product associated with this cart item. **/
        protected Product $product,
        /** @var int The quantity of this cart item. **/
        protected int $quantity = 0
    ) {}

    /**
     * Retrieves the product associated with this cart item.
     *
     * @return Product The product associated with this cart item.
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * Retrieves the quantity of this cart item.
     *
     * @return int The quantity of this cart item.
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * Sets the quantity of this cart item.
     *
     * @param int $quantity The quantity to set.
     *
     * @return CartItem Returns the updated CartItem instance.
     */
    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * Retrieves the price of this cart item.
     *
     * If the price is explicitly set, returns the set price.
     * Otherwise, calculates the price based on the product's price and the quantity.
     *
     * @return float The price of this cart item.
     */
    public function getPrice(): float
    {
        if ($this->price) {
            return $this->price;
        }

        return $this->product->getPrice() * $this->quantity;
    }

    /**
     * Sets the price of this cart item.
     *
     * @param float $price The price to set.
     *
     * @return CartItem Returns the updated CartItem instance.
     */
    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    /**
     * Calculates the total price for the cart item.
     *
     * @return float The total price of the cart item, calculated by multiplying the price of the product by the quantity.
     */
    public function getFullPrice(): float
    {
        return $this->product->getPrice() * $this->quantity;
    }
}

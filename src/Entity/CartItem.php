<?php

namespace App\Entity;

class CartItem
{
    protected float $price = 0;

    public function __construct(
        protected Product $product,
        protected int $quantity = 0,
    ) {
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPrice(): float
    {
        if ($this->price) {
            return $this->price;
        }

        return $this->product->getPrice() * $this->quantity;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }
}

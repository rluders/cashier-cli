<?php

namespace App\Entity;

class Cart
{
    public function __construct(
        protected $items = [],
    ) {
    }

    public function addItem(CartItem $item): self
    {
        $this->items[] = $item;

        return $this;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getTotalPrice(): float
    {
        $totalPrice = 0;

        foreach ($this->items as $item) {
            $totalPrice += $item->getPrice();
        }

        return ceil($totalPrice * 100) / 100;
    }

    public function findCartItemByProductCode(string $productCode): ?CartItem
    {
        foreach ($this->items as $item) {
            if ($item->getProduct()->getCode() === $productCode) {
                return $item;
            }
        }

        return null;
    }

    public function isEmpty() : bool
    {
        return empty($this->items);
    }
}

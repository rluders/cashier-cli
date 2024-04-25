<?php

namespace App\Entity;

class Product
{
    public function __construct(
        protected string $code,
        protected string $name,
        protected float $price,
    ) {
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }
}

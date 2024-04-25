<?php

namespace App\Entity;

/**
 * Class Product
 *
 * Represents a product in the system.
 */
class Product
{
    /**
     * Product constructor.
     *
     * @param string $code The code of the product.
     * @param string $name The name of the product.
     * @param float $price The price of the product.
     */
    public function __construct(
        /** @var string The code of the product. **/
        protected string $code,
        /** @var string The name of the product. **/
        protected string $name,
        /** @var float The price of the product. **/
        protected float $price
    ) {}

    /**
     * Retrieves the code of the product.
     *
     * @return string The code of the product.
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Retrieves the name of the product.
     *
     * @return string The name of the product.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Retrieves the price of the product.
     *
     * @return float The price of the product.
     */
    public function getPrice(): float
    {
        return $this->price;
    }
}

<?php

namespace App\Service;

use App\Entity\Product;

/**
 * Class CatalogService
 *
 * This service class provides functionality related to managing a catalog of products.
 */
class CatalogService
{
    /**
     * @var array The array of products in the catalog.
     */
    private array $products = [];

    /**
     * CatalogService constructor.
     *
     * Initializes the catalog with some predefined products.
     */
    public function __construct()
    {
        // Just to use in memory, but... You know...
        $this->products['GR1'] = new Product('GR1', 'Green Tea', 3.11);
        $this->products['CF1'] = new Product('CF1', 'Coffee', 11.23);
        $this->products['SR1'] = new Product('SR1', 'Strawberries', 5.00);
    }

    /**
     * Retrieves a product from the catalog by its code.
     *
     * @param string $code The product code
     * @return Product|null The product if found, otherwise null
     */
    public function getProduct(string $code): ?Product
    {
        return $this->products[$code] ?? null;
    }

    /**
     * Retrieves all products in the catalog.
     *
     * @return array The array of products
     */
    public function getAllProducts(): array
    {
        return $this->products;
    }
}
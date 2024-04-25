<?php

use App\Entity\Product;
use App\Service\CatalogService;

it('should retrieve a product from the catalog by its code', function () {
    $catalogService = new CatalogService();
    $expectedProduct = new Product('GR1', 'Green Tea', 3.11);

    $retrievedProduct = $catalogService->getProduct('GR1');

    expect($retrievedProduct)->toEqual($expectedProduct);
});

it('should return null when retrieving a non-existing product from the catalog', function () {
    $catalogService = new CatalogService();

    $retrievedProduct = $catalogService->getProduct('XYZ');

    expect($retrievedProduct)->toBeNull();
});

it('should retrieve all products in the catalog', function () {
    $catalogService = new CatalogService();

    $allProducts = $catalogService->getAllProducts();

    expect($allProducts)->toBeArray();
});


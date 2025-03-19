<?php

namespace App\Models;

use App\Entities\ProductEntity;

class Product extends AbstractModel
{
    public function getAll(): array
    {
        $products = $this->entityManager->getRepository(ProductEntity::class)->findAll();
        $this->databaseLog(ProductEntity::class);
        return array_map(function ($product) {
            return [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'brand' => $product->getBrand(),
                'inStock' => $product->isInStock(),
                'description' => $product->getDescription(),
                'price' => $product->getPrices()[0], // don't look here :|
                'gallery' => $product->getGallery(),
                'category' => $product->getCategory(),
                'attributes' => $product->getAttributes(),
            ];
        }, $products);
    }

    public function getProduct($id): array|null
    {
        $product = $this->entityManager->getRepository(ProductEntity::class)->find($id);
        $this->databaseLog(ProductEntity::class);

        if (!$product) {
            return null;
        }

        return [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'brand' => $product->getBrand(),
            'inStock' => $product->isInStock(),
            'description' => $product->getDescription(),
            'price' => $product->getPrices()[0],
            'gallery' => $product->getGallery(),
            'category' => $product->getCategory(),
            'attributes' => $product->getAttributes(),
        ];
    }

    /**
     * Get products by category with a limit
     *
     * @param int|null $categoryId The category ID to filter by, or null for all categories
     * @param int $limit Maximum number of products to return
     * @return array Array of formatted product data
     */
    public function getProductsByCategory(?int $categoryId = null, int $limit = 10): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('p')
            ->from(ProductEntity::class, 'p')
            ->setMaxResults($limit);

        if ($categoryId !== null) {
            $queryBuilder->join('p.category', 'c')
                ->where('c.id = :categoryId')
                ->setParameter('categoryId', $categoryId);
        }

        $products = $queryBuilder->getQuery()->getResult();
        $this->databaseLog(ProductEntity::class);

        return array_map(function ($product) {
            $prices = $product->getPrices();
            $price = !$prices->isEmpty() ? $prices->first() : null;

            return [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'brand' => $product->getBrand(),
                'inStock' => $product->isInStock(),
                'description' => $product->getDescription(),
                'price' => $price,
                'gallery' => $product->getGallery(),
                'category' => $product->getCategory(),
                'attributes' => $product->getAttributes(),
            ];
        }, $products);
    }
}

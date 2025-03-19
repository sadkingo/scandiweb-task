<?php

namespace App\Models;

use App\Entities\CurrencyEntity;
use App\Entities\OrderEntity;
use App\Entities\ProductEntity;
use App\Entities\AttributeItemsEntity;
use Doctrine\ORM\Exception\ORMException;
use Exception;

class Order extends AbstractModel
{
    /**
     * Get all orders
     *
     * @return array
     */
    public function getAll(): array
    {
        $orders = $this->entityManager->getRepository(OrderEntity::class)->findAll();
        $this->databaseLog(OrderEntity::class);

        return array_map(function ($order) {
            return [
                'id' => $order->getId(),
                'orderedProducts' => $order->getOrderedProducts(),
                'total' => $order->getTotal(),
                'currency' => $order->getCurrency(),
                'created_at' => $order->getCreatedAt()->format('Y-m-d H:i:s')
            ];
        }, $orders);
    }

    /**
     * Get a specific order by ID
     *
     * @param int $id
     * @return array|null
     */
    public function getOrder(int $id): ?array
    {
        $order = $this->entityManager->getRepository(OrderEntity::class)->find($id);
        $this->databaseLog(OrderEntity::class);

        if (!$order) {
            return null;
        }

        return [
            'id' => $order->getId(),
            'orderedProducts' => $order->getOrderedProducts(),
            'total' => $order->getTotal(),
            'currency' => $order->getCurrency(),
            'created_at' => $order->getCreatedAt()->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Validate product IDs and selected attributes
     *
     * @param array $productsData
     * @return array [isValid, errors]
     */
    private function validateProducts(array $productsData): array
    {
        $errors = [];
        $productRepo = $this->entityManager->getRepository(ProductEntity::class);
        $attributeItemRepo = $this->entityManager->getRepository(AttributeItemsEntity::class);

        foreach ($productsData as $index => $productData) {
            // Check if product exists
            $product = $productRepo->find($productData['id']);
            if (!$product) {
                $errors[] = "Product with ID '{$productData['id']}' not found";
                throw new Exception('Order validation failed: ' . implode(', ', $errors));
            }

            // Check if product is in stock
            if (!$product->isInStock()) {
                $errors[] = "Product '{$product->getName()}' is out of stock";
                throw new Exception('Order validation failed: ' . implode(', ', $errors));
            }

            // Validate selected attributes if provided
            if (isset($productData['selectedAttributes']) && !empty($productData['selectedAttributes'])) {
                $selectedAttributeIds = is_string($productData['selectedAttributes'])
                    ? json_decode($productData['selectedAttributes'], true)
                    : $productData['selectedAttributes'];

                if (is_array($selectedAttributeIds)) {
                    $productAttributes = $product->getAttributes();
                    $validAttributeItems = [];

                    // Check each selected attribute item ID
                    foreach ($selectedAttributeIds as $attributeItemId) {
                        $attributeItem = $attributeItemRepo->find($attributeItemId);

                        if (!$attributeItem) {
                            $errors[] = "Attribute item with ID '{$attributeItemId}' not found";
                            throw new Exception('Order validation failed: ' . implode(', ', $errors));
                        }

                        $attribute = $attributeItem->getAttribute();
                        if (!$attribute) {
                            $errors[] = "Attribute item with ID '{$attributeItemId}' does not have a parent attribute";
                            throw new Exception('Order validation failed: ' . implode(', ', $errors));
                        }

                        // Check if this attribute belongs to the product
                        $attributeBelongsToProduct = false;
                        foreach ($productAttributes as $productAttribute) {
                            if ($productAttribute->getId() === $attribute->getId()) {
                                $attributeBelongsToProduct = true;
                                break;
                            }
                        }

                        if (!$attributeBelongsToProduct) {
                            $errors[] = "Attribute '{$attribute->getName()}' does not exist for product '{$product->getName()}'";
                            throw new Exception('Order validation failed: ' . implode(', ', $errors));
                        } else {
                            $validAttributeItems[] = $attributeItem;
                        }
                    }

                    // Store valid attribute items for later use
                    $productData['validAttributeItems'] = $validAttributeItems;
                }
            }
        }

        return [empty($errors), $errors];
    }

    /**
     * Create a new order
     *
     * @param array $data
     * @return array
     * @throws Exception If validation fails
     */
    public function createOrder(array $data): array
    {
        // Validate products and attributes
        [$isValid, $errors] = $this->validateProducts($data['products']);

        if (!$isValid) {
            throw new Exception('Order validation failed: ' . implode(', ', $errors));
        }

        $order = new OrderEntity();

        // Set currency
        $currency = $this->entityManager->getRepository(CurrencyEntity::class)->find($data['currency_id']);
        if (!$currency) {
            throw new Exception('Currency not found');
        }
        $order->setCurrency($currency);

        // Add products to order
        foreach ($data['products'] as $productData) {
            $product = $this->entityManager->getRepository(ProductEntity::class)->find($productData['id']);

            // Use the validated attribute items if available, otherwise use an empty array
            $selectedAttributes = json_decode($productData['selectedAttributes']) ?? [];
            $order->addProduct(
                $product,
                $productData['quantity'],
                $selectedAttributes
            );
        }

        $this->entityManager->persist($order);
        $this->entityManager->flush();
        $this->databaseLog(OrderEntity::class);

        return [
            'id' => $order->getId(),
            'orderedProducts' => $order->getOrderedProducts(),
            'total' => $order->getTotal(),
            'currency' => $order->getCurrency(),
            'created_at' => $order->getCreatedAt()->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Update an order
     *
     * @param int $id
     * @param array $data
     * @return array|null
     * @throws Exception|ORMException If validation fails
     */
    public function updateOrder(int $id, array $data): ?array
    {
        $order = $this->entityManager->getRepository(OrderEntity::class)->find($id);

        if (!$order) {
            return null;
        }

        if (isset($data['products'])) {
            // Validate products and attributes
            [$isValid, $errors] = $this->validateProducts($data['products']);

            if (!$isValid) {
                throw new Exception('Order update validation failed: ' . implode(', ', $errors));
            }

            // Remove all existing items
            foreach ($order->getItems() as $item) {
                $order->removeItem($item);
                $this->entityManager->remove($item);
            }

            // Add new products
            foreach ($data['products'] as $productData) {
                $product = $this->entityManager->getRepository(ProductEntity::class)->find($productData['id']);

                // Use the validated attribute items if available, otherwise use an empty array
                $selectedAttributes = $productData['validAttributeItems'] ?? [];

                $order->addProduct(
                    $product,
                    $productData['quantity'],
                    $selectedAttributes
                );
            }
        }

        if (isset($data['currency_id'])) {
            $currency = $this->entityManager->getRepository(CurrencyEntity::class)->find($data['currency_id']);
            $order->setCurrency($currency);
        }

        $this->entityManager->flush();
        $this->databaseLog(OrderEntity::class);

        return [
            'id' => $order->getId(),
            'orderedProducts' => $order->getOrderedProducts(),
            'total' => $order->getTotal(),
            'currency' => $order->getCurrency(),
            'created_at' => $order->getCreatedAt()->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Delete an order
     *
     * @param int $id
     * @return bool
     */
    public function deleteOrder(int $id): bool
    {
        $order = $this->entityManager->getRepository(OrderEntity::class)->find($id);

        if (!$order) {
            return false;
        }

        $this->entityManager->remove($order);
        $this->entityManager->flush();
        $this->databaseLog(OrderEntity::class);

        return true;
    }
}
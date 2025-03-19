<?php

namespace App\Models;

use App\Entities\AttributeEntity;
use App\Entities\AttributeItemsEntity;

class Attribute extends AbstractModel
{
    /**
     * Get all attributes
     * 
     * @return array
     */
    public function getAll(): array
    {
        $attributes = $this->entityManager->getRepository(AttributeEntity::class)->findAll();
        $this->databaseLog(AttributeEntity::class);
        
        return array_map(function ($attribute) {
            return [
                'id' => $attribute->getId(),
                'name' => $attribute->getName(),
                'type' => $attribute->getType(),
                'items' => $this->formatAttributeItems($attribute->getAttributeItems()->toArray())
            ];
        }, $attributes);
    }
    
    /**
     * Get a specific attribute by ID
     * 
     * @param int $id
     * @return array|null
     */
    public function getAttribute(int $id): ?array
    {
        $attribute = $this->entityManager->getRepository(AttributeEntity::class)->find($id);
        $this->databaseLog(AttributeEntity::class);
        
        if (!$attribute) {
            return null;
        }
        
        return [
            'id' => $attribute->getId(),
            'name' => $attribute->getName(),
            'type' => $attribute->getType(),
            'items' => $this->formatAttributeItems($attribute->getAttributeItems()->toArray())
        ];
    }

    /**
     * Create a new attribute
     * 
     * @param array $data Attribute data including name, type, and items
     * @return array|null The created attribute or null if creation failed
     */
    public function createAttribute(array $data): ?array
    {
        if (empty($data['name']) || empty($data['type'])) {
            return null;
        }

        $attribute = new AttributeEntity();
        $attribute->setName($data['name']);
        $attribute->setType($data['type']);

        // Add attribute items if provided
        if (!empty($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $itemData) {
                if (!empty($itemData['displayValue']) && !empty($itemData['value'])) {
                    $item = new AttributeItemsEntity();
                    $item->setDisplayValue($itemData['displayValue']);
                    $item->setValue($itemData['value']);
                    $attribute->addAttributeItem($item);
                }
            }
        }

        try {
            $this->entityManager->persist($attribute);
            $this->entityManager->flush();
            $this->databaseLog(AttributeEntity::class);

            return [
                'id' => $attribute->getId(),
                'name' => $attribute->getName(),
                'type' => $attribute->getType(),
                'items' => $this->formatAttributeItems($attribute->getAttributeItems()->toArray())
            ];
        } catch (\Exception $e) {
            // Log the error
            error_log("Error creating attribute: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update an existing attribute
     * 
     * @param int $id Attribute ID
     * @param array $data Updated attribute data
     * @return array|null The updated attribute or null if update failed
     */
    public function updateAttribute(int $id, array $data): ?array
    {
        $attribute = $this->entityManager->getRepository(AttributeEntity::class)->find($id);
        $this->databaseLog(AttributeEntity::class);

        if (!$attribute) {
            return null;
        }

        // Update attribute properties if provided
        if (!empty($data['name'])) {
            $attribute->setName($data['name']);
        }

        if (!empty($data['type'])) {
            $attribute->setType($data['type']);
        }

        // Update attribute items if provided
        if (!empty($data['items']) && is_array($data['items'])) {
            // Remove existing items and add new ones
            $existingItems = $attribute->getAttributeItems();
            foreach ($existingItems as $item) {
                $this->entityManager->remove($item);
            }

            foreach ($data['items'] as $itemData) {
                if (!empty($itemData['displayValue']) && !empty($itemData['value'])) {
                    $item = new AttributeItemsEntity();
                    $item->setDisplayValue($itemData['displayValue']);
                    $item->setValue($itemData['value']);
                    $attribute->addAttributeItem($item);
                }
            }
        }

        try {
            $this->entityManager->flush();
            $this->databaseLog(AttributeEntity::class);

            return [
                'id' => $attribute->getId(),
                'name' => $attribute->getName(),
                'type' => $attribute->getType(),
                'items' => $this->formatAttributeItems($attribute->getAttributeItems()->toArray())
            ];
        } catch (\Exception $e) {
            // Log the error
            error_log("Error updating attribute: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete an attribute
     * 
     * @param int $id Attribute ID
     * @return bool True if deletion was successful, false otherwise
     */
    public function deleteAttribute(int $id): bool
    {
        $attribute = $this->entityManager->getRepository(AttributeEntity::class)->find($id);
        $this->databaseLog(AttributeEntity::class);

        if (!$attribute) {
            return false;
        }

        try {
            $this->entityManager->remove($attribute);
            $this->entityManager->flush();
            $this->databaseLog(AttributeEntity::class);
            return true;
        } catch (\Exception $e) {
            // Log the error
            error_log("Error deleting attribute: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Format attribute items for API response
     * 
     * @param array $items Array of AttributeItemsEntity objects
     * @return array Formatted attribute items
     */
    private function formatAttributeItems(array $items): array
    {
        return array_map(function ($item) {
            return [
                'id' => $item->getId(),
                'displayValue' => $item->getDisplayValue(),
                'value' => $item->getValue()
            ];
        }, $items);
    }
}
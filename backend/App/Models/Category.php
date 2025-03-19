<?php

namespace App\Models;

use App\Entities\CategoryEntity;

class Category extends AbstractModel
{
    /**
     * Get all categories
     * 
     * @return array
     */
    public function getAll(): array
    {
        $categories = $this->entityManager->getRepository(CategoryEntity::class)->findAll();
        $this->databaseLog(CategoryEntity::class);
        
        return array_map(function ($category) {
            return [
                'id' => $category->getId(),
                'name' => $category->getName(),
            ];
        }, $categories);
    }
    
    /**
     * Get a specific category by ID
     * 
     * @param string $id
     * @return array|null
     */
    public function getCategory(string $id): ?array
    {
        $category = $this->entityManager->getRepository(CategoryEntity::class)->find($id);
        $this->databaseLog(CategoryEntity::class);
        
        if (!$category) {
            return null;
        }
        
        return [
            'id' => $category->getId(),
            'name' => $category->getName(),
        ];
    }
}
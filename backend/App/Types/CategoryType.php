<?php

namespace App\Types;

use App\Entities\CategoryEntity;
use App\Models\Category;
use Config\DoctrineManager;
use GraphQL\Type\Definition\Type;

/**
 * This class represents the GraphQL type for a Category.
 * It extends the BaseType class and implements the necessary methods for GraphQL schema.
 */
class CategoryType extends BaseType
{
    public function getSchemaFields(): array
    {
        return [
            'id' => Type::nonNull(Type::ID()),
            'name' => Type::nonNull(Type::STRING()),
        ];
    }

    public function resolve($args = []): array
    {
        $categoryModel = new Category();
        
        if (isset($args['category']) && $args['category'] instanceof CategoryEntity) {
            $category = $args['category'];
            return [
                'id' => $category->getId(),
                'name' => $category->getName(),
            ];
        } 
        if (isset($args['id'])) {
            return $categoryModel->getCategory($args['id']) ?? [];
        }
        return $categoryModel->getAll();
    }
}
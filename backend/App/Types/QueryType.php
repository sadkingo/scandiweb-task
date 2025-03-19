<?php

namespace App\Types;

use App\Models\Product;
use GraphQL\Type\Definition\Type;

class QueryType extends BaseType
{
    protected BaseType $categoryType;
    protected BaseType $productType;

    public function __construct()
    {
        $this->categoryType = TypeRegistry::type(CategoryType::class);
        $this->productType = TypeRegistry::type(ProductType::class);
        parent::__construct();
    }

    protected function getSchemaFields(): array
    {
        $productModel = new Product();
        return [
            'products' => [
                'type' => Type::listOf($this->productType),
                'args' => [
                    'categoryId' => [
                        'type' => Type::int(),
                        'description' => 'Optional category ID to filter products'
                    ],
                    'limit' => [
                        'type' => Type::int(),
                        'description' => 'Maximum number of products to return',
                        'defaultValue' => 10
                    ]
                ],
                'resolve' => function ($_, array $args) use ($productModel) {
                    if (isset($args['categoryId'])) {
                        return $productModel->getProductsByCategory(
                            $args['categoryId'], 
                            $args['limit'] ?? 10
                        );
                    }
                    return $productModel->getAll();
                },
            ],
            'product' => [
                'type' => $this->productType,
                'args' => [
                    'id' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Product ID'
                    ],
                ],
                'resolve' => function ($_, array $args) {
                    return $this->productType->resolve($args);
                },
            ],
            'categories' => [
                'type' => Type::listOf($this->categoryType),
                'resolve' => function ($_, $args) {
                    return $this->categoryType->resolve($args);
                },
            ],
        ];
    }

    public function resolve($args): array
    {
        return [];
    }
}

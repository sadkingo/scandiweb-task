<?php

namespace App\Types;


use App\Entities\AttributeItemsEntity;
use App\Entities\ProductEntity;
use App\Entities\GalleryEntity;
use App\Models\Product;
use Config\DoctrineManager;
use GraphQL\Type\Definition\Type;

class ProductType extends BaseType
{
    public function getSchemaFields(): array
    {
        return [
            'id' => Type::nonNull(Type::string()),
            'name' => Type::nonNull(Type::string()),
            'brand' => Type::string(),
            'inStock' => Type::nonNull(Type::boolean()),
            'description' => Type::string(),
            'gallery' => [
                'type' => Type::listOf(Type::string()),
                'resolve' => function ($args) {
                    return array_map(function ($image) {
                        return $image->getImageurl();
                        return $image->getImageUrl();
                    }, $args['gallery']->toArray());
                }
            ],
            'category' => [
                'type' => TypeRegistry::type(CategoryType::class),
                'resolve' => function ($args) {
                    return TypeRegistry::type(CategoryType::class)->resolve($args);
                }
            ],
            'price' => [
                'type' => TypeRegistry::type(PriceType::class),
                'resolve' => function ($args) {
                    return TypeRegistry::type(PriceType::class)->resolve($args);
                }
            ],
            'currency' => [
                'type' => TypeRegistry::type(CurrencyType::class),
                'resolve' => function ($args) {
                    return TypeRegistry::type(CurrencyType::class)->resolve($args);
                }
            ],
            'attributes' => [
                'type' => Type::listOf(TypeRegistry::type(AttributeType::class)),
                'resolve' => function ($args) {
                    return TypeRegistry::type(AttributeType::class)->resolve($args);
                }
            ]
        ];
    }

    public function resolve($args): array
    {
        if (isset($args['product']) && $args['product'] instanceof ProductEntity) {
            $product = $args['product'];

            return [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'brand' => $product->getBrand(),
                'inStock' => $product->isInStock(),
                'description' => $product->getDescription(),
                'gallery' => $product->getGallery(),
                'price' => $product->getPrices()[0],
                'currency' => $product->getPrices()[0]->getCurrency(),
                'category' => $product->getCategory(),
                'attributes' => $product->getAttributes()
            ];
        }
        if (isset($args['id'])) {
            $productModel = new Product();
            $productData = $productModel->getProduct($args['id']);

            if (!$productData) {
                return [];
            }

            return $productData;
        }
        return [];
    }
}
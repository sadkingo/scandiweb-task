<?php

namespace App\Types;

use App\Entities\AttributeEntity;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * This class represents the GraphQL type for an Attribute.
 * It extends the BaseType class and implements the necessary methods for GraphQL schema.
 */
class AttributeType extends BaseType
{
    protected function getSchemaFields(): array
    {
        return [
            'id' => Type::id(),
            'name' => Type::string(),
            'type' => Type::string(),
            'items' => [
                'type' => Type::listOf(
                    new ObjectType([
                        'name' => 'AttributeItem',
                        'fields' => [
                            'id' => Type::string(),
                            'value' => Type::string(),
                            'displayValue' => Type::string(),
                        ]
                    ])
                ),
                'resolve' => function ($attr) {
                    return array_map(fn($item) => [
                        'id' => $item->getId(),
                        'value' => $item->getValue(),
                        'displayValue' => $item->getDisplayValue(),
                    ], $attr['items']->toArray());
                },
            ],
        ];
    }

    public function resolve($args = []): array
    {
        $attributes = $args['attributes'];
        return array_map(function (AttributeEntity $entity) {
            return [
                'id' => $entity->getId(),
                'name' => $entity->getName(),
                'type' => $entity->getType(),
                'items' => $entity->getItems(),
            ];
        }, $attributes->toArray());
    }
}
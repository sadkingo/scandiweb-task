<?php

namespace App\Types;

use App\Entities\PriceEntity;
use GraphQL\Type\Definition\Type;

/**
 * This class represents the GraphQL type for an Price.
 * It extends the BaseType class and implements the necessary methods for GraphQL schema.
 */
class PriceType extends BaseType
{
    public function getSchemaFields(): array
    {
        return [
            'amount' => Type::string(),
            'currency' => [
                'type' => TypeRegistry::type(CurrencyType::class),
                'resolve' => function ($args) {
                    return TypeRegistry::type(CurrencyType::class)->resolve($args);
                }
            ],
        ];
    }

    public function resolve($args = []): array
    {
        $price = $args['price'];
        $currency = $price->getCurrency();
        return
            [
                'amount' => $price->getAmount(),
                'currency' => $currency,
            ];
    }
}
<?php

namespace App\Types;

use GraphQL\Type\Definition\Type;

/**
 * This class represents the GraphQL type for a CurrencyType.
 * It extends the BaseType class and implements the necessary methods for GraphQL schema.
 */
class CurrencyType extends BaseType
{
    public function getSchemaFields(): array
    {
        return [
            'label' => Type::nonNull(Type::string()),
            'symbol' => Type::nonNull(Type::string()),
        ];
    }

    public function resolve($args = []): array
    {
        $currency = $args['currency'];
        return [
            'label' => $currency->getLabel(),
            'symbol' => $currency->getSymbol(),
        ];
    }
}
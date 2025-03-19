<?php

namespace App\Types;

use App\Entities\OrderEntity;
use App\Models\Order as OrderModel;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class OrderType extends BaseType
{
    public function getSchemaFields(): array
    {
        $orderedProductType = new ObjectType([
            'name' => 'OrderedProduct',
            'fields' => [
                'product_id' => ['type' => Type::string()],
                'quantity' => ['type' => Type::int()],
                'unit_price' => ['type' => Type::float()],
                'total' => ['type' => Type::float()],
                'selected_attributes' => ['type' => Type::listOf(Type::string())]
            ]
        ]);
        return [
            'id' => Type::nonNull(Type::int()),
            'orderedProducts' => [
                'type' => Type::listOf($orderedProductType),
                'resolve' => function ($args) {
                    return $args['orderedProducts'];
                }
            ],
            'total' => Type::nonNull(Type::float()),
            'currency' => [
                'type' => TypeRegistry::type(CurrencyType::class),
                'resolve' => function ($args) {
                    return TypeRegistry::type(CurrencyType::class)->resolve(['currency' => $args['currency']]);
                }
            ],
            'created_at' => Type::nonNull(Type::string())
        ];
    }

    public function resolve($args): array
    {
        if (isset($args['order']) && $args['order'] instanceof OrderEntity) {
            $order = $args['order'];
            return [
                'id' => $order->getId(),
                'orderedProducts' => $order->getOrderedProducts(),
                'total' => $order->getTotal(),
                'currency' => $order->getCurrency(),
                'created_at' => $order->getCreatedAt()->format('Y-m-d H:i:s')
            ];
        } elseif (isset($args['id'])) {
            $orderModel = new OrderModel();
            return $orderModel->getOrder($args['id']) ?? [];
        } else {
            return [];
        }
    }
}
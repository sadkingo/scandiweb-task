<?php

namespace App\Types;

use App\Models\Order;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\InputObjectType;

class MutationType extends BaseType
{
    protected BaseType $orderType;

    public function __construct()
    {
        $this->orderType = TypeRegistry::type(OrderType::class);
        parent::__construct();
    }

    protected function getSchemaFields(): array
    {
        $orderModel = new Order();

        // Define product input type for order creation
        $productInputType = new InputObjectType([
            'name' => 'ProductInput',
            'fields' => [
                'id' => Type::nonNull(Type::string()),
                'quantity' => Type::nonNull(Type::int()),
                'selectedAttributes' => Type::string(),
            ]
        ]);

        return [
            'createOrder' => [
                'type' => $this->orderType,
                'args' => [
                    'products' => [
                        'type' => Type::nonNull(Type::listOf($productInputType)),
                        'description' => 'List of products with id, quantity, and selected attributes'
                    ],
                    'currency_id' => [
                        'type' => Type::nonNull(Type::int()),
                        'description' => 'Currency ID'
                    ]
                ],
                'resolve' => function ($_, array $args) use ($orderModel) {
                    try {
                        return $orderModel->createOrder([
                            'products' => $args['products'],
                            'currency_id' => $args['currency_id']
                        ]);
                    } catch (\Exception $e) {
                        throw new \GraphQL\Error\Error($e->getMessage());
                    }
                }
            ],
            'updateOrder' => [
                'type' => $this->orderType,
                'args' => [
                    'id' => [
                        'type' => Type::nonNull(Type::int()),
                        'description' => 'Order ID'
                    ],
                    'products' => [
                        'type' => Type::listOf($productInputType),
                        'description' => 'List of products with id, quantity, and selected attributes'
                    ],
                    'currency_id' => [
                        'type' => Type::int(),
                        'description' => 'Currency ID'
                    ]
                ],
                'resolve' => function ($_, array $args) use ($orderModel) {
                    $updateData = [];

                    if (isset($args['products'])) {
                        $updateData['products'] = $args['products'];
                    }

                    if (isset($args['currency_id'])) {
                        $updateData['currency_id'] = $args['currency_id'];
                    }

                    return $orderModel->updateOrder($args['id'], $updateData);
                }
            ],
            'deleteOrder' => [
                'type' => Type::boolean(),
                'args' => [
                    'id' => [
                        'type' => Type::nonNull(Type::int()),
                        'description' => 'Order ID'
                    ]
                ],
                'resolve' => function ($_, array $args) use ($orderModel) {
                    return $orderModel->deleteOrder($args['id']);
                }
            ]
        ];
    }

    public function resolve($args): array
    {
        // This method is required by BaseType but not used for mutations
        return [];
    }
}
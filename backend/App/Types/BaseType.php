<?php

namespace App\Types;

use Config\DoctrineManager;
use Doctrine\ORM\EntityManager;
use GraphQL\Type\Definition\ObjectType;
use ReflectionClass;

/**
 * This class serves as a base for GraphQL types in the application.
 * It provides common functionality for GraphQL types, such as fetching the EntityManager instance
 * and dynamically generating the type name based on the class name.
 */
abstract class BaseType extends ObjectType
{
    /**
     * @var EntityManager|null A static property to hold the EntityManager instance.
     */
    static ?EntityManager $entityManager = null;

    /**
     * This method should be implemented by child classes to define the fields for the GraphQL type.
     *
     * @return array An array of field definitions for the GraphQL type.
     */
    abstract protected function getSchemaFields(): array;

    /**
     * Resolves the GraphQL query and returns the data.
     * 
     * This method should be implemented by child classes to handle data resolution
     * for GraphQL queries. It processes the provided arguments and returns
     * the appropriate data in the format expected by GraphQL.
     *
     * @param array $args Arguments passed from the GraphQL query
     * @return array The resolved data in array format
     */
    abstract public function resolve($args): array;

    /**
     * The constructor initializes the EntityManager instance and sets up the GraphQL type.
     */
    public function __construct()
    {
        // Fetch the EntityManager instance from DoctrineManager
        self::$entityManager = DoctrineManager::getEntityManagerInstance();

        // Call the parent constructor with the type name and fields
        parent::__construct([
            'name' => static::getTypeName(),
            'fields' => $this->getSchemaFields(),
        ]);
    }

    /**
     * This method generates the type name based on the class name.
     * It removes the "Type" suffix from the class name.
     *
     * @return string The generated type name.
     */
    private static function getTypeName(): string
    {
        $className = (new ReflectionClass(static::class))->getShortName();
        return str_replace('Type', '', $className);
    }
}

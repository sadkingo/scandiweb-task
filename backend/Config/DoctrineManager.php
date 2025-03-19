<?php

namespace Config;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

/**
 * DoctrineManager class
 * 
 * Manages Doctrine ORM connections and entity manager instances.
 * Implements a singleton pattern to ensure only one connection and entity manager exist.
 */
class DoctrineManager
{
    /** @var Connection|null Database connection instance */
    private static ?Connection $connection = null;
    
    /** @var EntityManager|null Entity manager instance */
    private static ?EntityManager $entityManager = null;

    /**
     * Initialize the Doctrine connection and entity manager
     * 
     * Sets up the database connection and entity manager with appropriate configuration
     * for entity mapping, proxy generation, and database access.
     * 
     * @return void
     */
    private static function initialize(): void
    {
        $dbParams = require_once 'db_params.php';
        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: [__DIR__ . '/../App/Entities'],
            isDevMode: false,
        );
        $config->setProxyDir(__DIR__ . '/../App/Proxies');
        $config->setAutoGenerateProxyClasses(true);
        self::$connection = DriverManager::getConnection($dbParams);
        self::$entityManager = new EntityManager(self::$connection, $config);
    }

    /**
     * Get the database connection instance
     * 
     * Returns the existing connection or initializes a new one if none exists.
     * 
     * @return Connection The database connection
     */
    public static function getConnection(): Connection
    {
        if (self::$connection === null) {
            self::initialize();
        }
        return self::$connection;
    }

    /**
     * Get the entity manager instance
     * 
     * Returns the existing entity manager or initializes a new one if none exists.
     * 
     * @return EntityManager The entity manager
     */
    public static function getEntityManagerInstance(): EntityManager
    {
        if (self::$entityManager === null) {
            self::initialize();
        }
        return static::$entityManager;
    }
}
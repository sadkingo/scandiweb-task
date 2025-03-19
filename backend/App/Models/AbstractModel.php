<?php

namespace App\Models;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

/**
 * AbstractModel class
 *
 * Base class for all models in the application.
 * Provides database connection and entity manager functionality.
 */
abstract class AbstractModel
{
    /**
     * @var Connection|null Database connection instance
     */
    protected ?Connection $connection = null;

    /**
     * @var EntityManager|null Entity manager instance
     */
    protected ?EntityManager $entityManager = null;

    /**
     * Constructor
     *
     * Initializes the database connection and entity manager
     */
    public function __construct()
    {
        $this->initialize();
    }

    /**
     * Initialize the database connection and entity manager
     *
     * @return void
     */
    private function initialize(): void
    {
        $dbParams = require __DIR__ . '/../../Config/db_params.php';
        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: [__DIR__ . '/../../App/Entities'],
            isDevMode: false,
        );
        $config->setProxyDir(__DIR__ . '/../../App/Proxies');
        $config->setAutoGenerateProxyClasses(true);
        $this->connection = DriverManager::getConnection($dbParams);
        $this->entityManager = new EntityManager($this->connection, $config);
    }

    /**
     * Get the database connection
     *
     * @return Connection The database connection
     */
    public function getConnection(): Connection
    {
        if ($this->connection === null) {
            $this->initialize();
        }
        return $this->connection;
    }

    /**
     * Get the entity manager
     *
     * @return EntityManager The entity manager
     */
    public function getEntityManagerInstance(): EntityManager
    {
        if ($this->entityManager === null) {
            $this->initialize();
        }
        return $this->entityManager;
    }

    /**
     * Log database operations to a file
     *
     * @param string $entityClass The entity class being queried
     * @return void
     */
    protected function databaseLog($entityClass)
    {
        error_log(
            "[" . date('Y-m-d H:i:s') . "] Query executed: " . strtolower(
                (new \ReflectionClass($entityClass))->getShortName()
            ) .
            ". Stack trace:" . json_encode(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true) . "\n",
            3,
            dirname(__DIR__, 2) . '/logs/query.log'
        );
    }
}
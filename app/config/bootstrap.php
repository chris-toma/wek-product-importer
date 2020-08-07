<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once "./../vendor/autoload.php";

// Create a simple "default" Doctrine ORM configuration for Annotations
$isDevMode = true;
$proxyDir = null;
$cache = null;
$useSimpleAnnotationReader = false;
$config = Setup::createAnnotationMetadataConfiguration(
    [__DIR__ . "/src"], $isDevMode, $proxyDir, $cache, $useSimpleAnnotationReader
);

// database configuration parameters
$conn = [
    'driver'   => 'pdo_mysql',
    'user'     => 'root',
    'password' => $_ENV['MYSQL_ROOT_PASSWORD'],
    'dbname'   => $_ENV['MYSQL_DATABASE'],
    'host'     => 'sandbox_mysql',
];

// obtaining the entity manager
$entityManager = EntityManager::create($conn, $config);
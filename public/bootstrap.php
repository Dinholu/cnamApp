<?php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
date_default_timezone_set('America/Lima');
require_once "vendor/autoload.php";
$isDevMode = true;
$config = Setup::createYAMLMetadataConfiguration(array(__DIR__ . "/config/yaml"), $isDevMode);
$conn = array(
'host' => 'ec2-3-248-4-172.eu-west-1.compute.amazonaws.com',
'driver' => 'pdo_pgsql',
'user' => 'lmcibctugyfgla',
'password' => 'd6de1b827bf54f8fcb142f74f76c1d075eade5381e8843a11f8164eda91ce2e0',
'dbname' => 'd8jrr50unmfhsl',
'port' => '5432'
);
$entityManager = EntityManager::create($conn, $config);

<?php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
date_default_timezone_set('America/Lima');
require_once "vendor/autoload.php";
$isDevMode = true;
$config = Setup::createYAMLMetadataConfiguration(array(__DIR__ . "/config/yaml"), $isDevMode);
$conn = array(
'host' => 'dpg-ccuhk2kgqg4a9295cagg-a',
'driver' => 'pdo_pgsql',
'user' => 'cnam_user',
'password' => '0TJXmePJA9OkhPb6gllHoVZJIL6aOvDS',
'dbname' => 'cnam',
'port' => '5432'
);
$entityManager = EntityManager::create($conn, $config);

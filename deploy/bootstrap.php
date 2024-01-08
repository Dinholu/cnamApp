<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

date_default_timezone_set('America/Lima');
require_once "vendor/autoload.php";
$isDevMode = true;
$config = Setup::createYAMLMetadataConfiguration(array(__DIR__ . "/config/yaml"), $isDevMode);
$conn = array(
	'host' => 'dpg-cm23hba1hbls73bu41g0-a.frankfurt-postgres.render.com',

	'driver' => 'pdo_pgsql',
	'user' => 'base_websi_alizee_user',
	'password' => 'Od9zEDTS7UMNf2b7XL4uyPKblYIptVRZ',
	'dbname' => 'base_websi_alizee',
	'port' => '5432'
);


$entityManager = EntityManager::create($conn, $config);

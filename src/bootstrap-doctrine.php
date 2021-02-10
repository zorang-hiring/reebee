<?php

/**
 * File to do all dependency injections and to execute application
 */

require_once(__DIR__ . '/vendor/autoload.php');

// configure doctrine
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use \Doctrine\ORM\EntityManagerInterface;
$isDevMode = true;
$dbParams = [
    'driver'   => 'pdo_mysql',
    'user'     => getenv('MYSQL_USER'),
    'password' => getenv('MYSQL_PASSWORD'),
    'dbname'   => getenv('MYSQL_DATABASE'),
    'host'     => getenv('MYSQL_ALIAS'),
    'charset'  => 'utf8mb4'
];
$config = Setup::createAnnotationMetadataConfiguration(
    [realpath(__DIR__ . '/app/Entity')],
    $isDevMode, null, null, false
);
$eventManager = new \Doctrine\Common\EventManager();
$eventManager->addEventSubscriber(new \App\Subscriber\PageSubscriber());
// configure entity manager provider
class GetEntityManager {
    static protected $em;
    static function setEm(EntityManagerInterface $em) {self::$em = $em;}
    static function getEm():EntityManagerInterface {return self::$em;}
}
GetEntityManager::setEm(
    EntityManager::create($dbParams, $config, $eventManager)
);
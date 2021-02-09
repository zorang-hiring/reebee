<?php
declare(strict_types=1);

/**
 * File to do all dependency injections and to execute application
 */

namespace App;

require_once('../vendor/autoload.php');

// doctrine
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
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
    [realpath(__DIR__ . '/Entity')],
    $isDevMode, null, null, false
);
$entityManager = EntityManager::create($dbParams, $config);

// repositories
$userRepository = $entityManager->getRepository(\App\Entity\User::class);
$flyerRepository = $entityManager->getRepository(\App\Entity\Flyer::class);

// service container init
use App\Service\Auth;
use App\Service\Flyer;
use App\Service\User;
$servicesContainer = new ServiceContainer();
$servicesContainer->addServices(Auth::ID, new Auth($userRepository));
$servicesContainer->addServices(Flyer::ID, new Flyer($flyerRepository));
$servicesContainer->addServices(User::ID, new User($userRepository));

// build request
$request = new Request($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']); // todo use getenv

// bootstrap app and print output
$app = new App($servicesContainer, $_ENV);
$app->output($request);
<?php
declare(strict_types=1);
namespace App;
require(__DIR__ . '/../bootstrap-doctrine.php');

// App bootstrap

// repositories
$userRepository = \GetEntityManager::getEm()->getRepository(\App\Entity\User::class);
$flyerRepository = \GetEntityManager::getEm()->getRepository(\App\Entity\Flyer::class);
$pageRepository = \GetEntityManager::getEm()->getRepository(\App\Entity\Page::class);

// service container init
use App\Service\Auth;
use App\Service\Flyer;
use App\Service\Page;
use App\Service\User;
$servicesContainer = new ServiceContainer();
$servicesContainer->addServices(Auth::ID, new Auth($userRepository));
$servicesContainer->addServices(Flyer::ID, new Flyer($flyerRepository));
$servicesContainer->addServices(Page::ID, new Page($pageRepository));
$servicesContainer->addServices(User::ID, new User($userRepository));

// build request
$request = new Request($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']); // todo use getenv
$request->setContent(file_get_contents('php://input'));
$request->setHeaders(getallheaders());

// bootstrap app and print output
$app = new App($servicesContainer, $_ENV);
$app->output($request);
<?php
declare(strict_types=1);

echo 'I am bootstrap!';

//namespace App;
//
//use App\Service\Auth;
//use App\Service\Flyer;
//use App\Service\User;
//
//// service container init
//$servicesContainer = new ServiceContainer();
//$servicesContainer->addServices(Auth::ID, new Auth());
//$servicesContainer->addServices(Flyer::ID, new Flyer());
//$servicesContainer->addServices(User::ID, new User());
//
//// build request
//$request = new Request($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
//
//// bootstrap app and print output
//$app = new App($servicesContainer, $_ENV);
//$app->output($request);
<?php
declare(strict_types=1);

namespace App\Controller;

use App\Request;
use App\Response;
use App\Service\Auth;
use App\ServiceContainer;

abstract class AbstractController
{
    /**
     * @var ServiceContainer
     */
    protected $services;

    /**
     * @param ServiceContainer $services
     */
    public function setServiceContainer(ServiceContainer $services)
    {
        $this->services = $services;
    }

    /**
     * @return Auth
     */
    protected function getAuthentication()
    {
        return $this->services->get(Auth::ID);
    }

}
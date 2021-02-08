<?php
declare(strict_types=1);

namespace App\Controller;

use App\Request;
use App\Response;
use App\ResponseJson;
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

    /**
     * @param array $body
     * @param int $status
     * @return ResponseJson
     */
    protected function newResponseJson($body, $status)
    {
        $r = new ResponseJson();
        $r->setBody($body);
        $r->setStatus($status);
        return $r;
    }
}
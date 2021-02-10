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

    protected function isAuthenticatedBasic(Request $request)
    {
        return !!$this->getAuthentication()->authenticateBasic($request);
    }

    protected function getResponseJson200($data)
    {
        return $this->newResponseJson(['status' => 'OK', 'data' => $data], 200);
    }

    protected function getResponseJson201($data)
    {
        return $this->newResponseJson(['status' => 'OK', 'data' => $data], 201);
    }

    protected function getResponseJson204()
    {
        return $this->newResponseJson(['status' => 'OK'], 204);
    }

    protected function getResponseJson400($errors)
    {
        return $this->newResponseJson(['status' => 'ERROR', 'errors' => $errors], 400);
    }

    protected function getResponseJson403()
    {
        return $this->newResponseJson(['status' => 'ERROR', 'errors' => 'Request not authorised.'], 403);
    }
}
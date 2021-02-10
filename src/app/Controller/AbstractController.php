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

    protected function getResponseJson200($data, $message = null)
    {
        $response = ['status' => 'OK'];
        if ($data !== null) {
            $response['data'] = $data;
        }
        if ($message) {
            $response['message'] = $message;
        }
        return $this->newResponseJson($response, 200);
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
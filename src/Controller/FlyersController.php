<?php
declare(strict_types=1);

namespace App\Controller;

use App\Request;
use App\Response;

class FlyersController extends AbstractController
{
    public function indexAction(Request $request)
    {
        return $this->listAction($request);
    }

    public function listAction(Request $request)
    {
        $response = new Response();
        $response->setStatus(200);
        $response->setBody(json_encode([]));
        return $response;
    }
}
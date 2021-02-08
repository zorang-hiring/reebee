<?php
declare(strict_types=1);

namespace App\Controller;

use App\Request;
use App\Response;

class FlyersController extends AbstractController
{
    public function indexAction(Request $request, Response $response)
    {
        $this->listAction( $request,  $response);
    }

    public function listAction(Request $request, Response $response)
    {
        $response->setStatus(200);
        $response->setBody(json_encode([]));
    }
}
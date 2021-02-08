<?php
declare(strict_types=1);

namespace App\Controller;

use App\Request;
use App\Response;
use App\Service\Auth;

class UsersController extends AbstractController
{
    public function indexAction(Request $request, Response $response)
    {
        if ($request->isPost()) {
            $this->postAction($request, $response);
            return;
        }

        $response->setStatus(404);
    }

    public function postAction(Request $request, Response $response)
    {
        if (!$this->getAuthentication()->isAllowedToCreateUsers($request)) {
            $response->setStatus(401);
        }




        // $response->setBody(json_encode([]));
    }
}
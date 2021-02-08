<?php
declare(strict_types=1);

namespace App\Controller;

use App\Request;
use App\ResponseJson;

class FlyersController extends AbstractController
{
    public function indexAction(Request $request)
    {
        return $this->listAction($request);
    }

    public function listAction(Request $request)
    {
        return $this->newResponseJson([], 200);
    }
}
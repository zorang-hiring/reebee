<?php
declare(strict_types=1);

namespace App\Controller;

use App\Request;
use App\Service\Flyer;

class FlyersController extends AbstractController
{
    public function indexAction(Request $request)
    {
        return $this->newResponseJson(
            $this->services->get(Flyer::ID)->findAllValid(),
            200
        );
    }

    public function getAction(Request $request)
    {
        return $this->newResponseJson(
            $this->services->get(Flyer::ID)->find($request->getPathParam('id')),
            200
        );
    }
}
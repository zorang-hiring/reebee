<?php
declare(strict_types=1);

namespace App\Controller;

use App\Form\FlyerSaveForm;
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

    public function postAction(Request $request)
    {
        if (!$this->getAuthentication()->authenticateBasic($request)) {
            return $this->newResponseJson([], 403);
        }

        $form = new FlyerSaveForm($request);
        if (!$form->isValid()) {
            return $this->newResponseJson(['errors' => $form->getErrors()], 400);
        }

        $flyer = new \App\Entity\Flyer();
        $this->services->get(Flyer::ID)->save($form->fillFlyer($flyer));

        return $this->newResponseJson($flyer, 201);
    }

    public function patchAction(Request $request)
    {
        return $this->newResponseJson([], 403);
    }

    public function deleteAction(Request $request)
    {
        return $this->newResponseJson([], 403);
    }
}
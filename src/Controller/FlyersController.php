<?php
declare(strict_types=1);

namespace App\Controller;

use App\Form\FlyerCreateForm;
use App\Form\FlyerSaveForm;
use App\Form\FlyerUpdateForm;
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

        $form = new FlyerCreateForm();
        $form->fillForm($request->getPostData());
        if (!$form->isValid()) {
            return $this->newResponseJson(['errors' => $form->getErrors()], 400);
        }

        $flyer = new \App\Entity\Flyer();
        $this->services->get(Flyer::ID)->save($form->fillFlyer($flyer));

        return $this->newResponseJson($flyer, 201);
    }

    public function patchAction(Request $request)
    {
        if (!$this->getAuthentication()->authenticateBasic($request)) {
            return $this->newResponseJson([], 403);
        }

        /** @var \App\Entity\Flyer $flyer */
        $flyer = $this->services->get(Flyer::ID)->find($request->getPathParam('id'));
        if (!$flyer) {
            return $this->newResponseJson(['message' => 'no such flyer'], 400);
        }

        $form = new FlyerUpdateForm();
        $form->fillForm($request->getPostData());
        if (!$form->isValid()) {
            return $this->newResponseJson(['errors' => $form->getErrors()], 400);
        }
        $form->fillFlyer($flyer);

        $this->services->get(Flyer::ID)->save($flyer);

        return $this->newResponseJson([], 204);
    }

    public function deleteAction(Request $request)
    {
        return $this->newResponseJson([], 403);
    }
}
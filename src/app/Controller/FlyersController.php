<?php
declare(strict_types=1);

namespace App\Controller;

use App\Form\FlyerCreateForm;
use App\Form\FlyerSaveForm;
use App\Form\FlyerUpdateForm;
use App\Request;
use App\Service\Flyer;
use OpenApi\Annotations as OA;

class FlyersController extends AbstractController
{
    /**
     * API GET: /flyers
     */
    public function indexAction(Request $request)
    {
        return $this->getResponseJson200(
            $this->services->get(Flyer::ID)->findAllValid()
        );
    }

    /**
     * API GET: /flyers/<id>
     */
    public function getAction(Request $request)
    {
        $flyer = $this->getFlyerById($request->getPathParam('id'));

        if (!$flyer) {
            return $this->getResponseJson400(['Flyer not found.']);
        }

        return $this->getResponseJson200($flyer);
    }

    /**
     * API GET: /flyers/<id>/pages
     */
    public function getPagesAction(Request $request)
    {
        $flyer = $this->getFlyerById($request->getPathParam('id'));

        if (!$flyer) {
            return $this->getResponseJson400(['Flyer not found.']);
        }

        return $this->getResponseJson200($flyer->getPages()->toArray());
    }

    /**
     * API POST: /flyers
     */
    public function postAction(Request $request)
    {
        if (!$this->isAuthenticatedBasic($request)) {
            return $this->getResponseJson403();
        }

        $form = new FlyerCreateForm($request->getData());
        if (!$form->isValid()) {
            return $this->getResponseJson400($form->getErrors());
        }

        $flyer = new \App\Entity\Flyer();
        $this->services->get(Flyer::ID)->save(
            $form->fillFlyer($flyer)
        );

        return $this->getResponseJson200($flyer);
    }

    /**
     * API PATCH: /flyers/<id>
     */
    public function patchAction(Request $request)
    {
        if (!$this->isAuthenticatedBasic($request)) {
            return $this->getResponseJson403();
        }

        $flyer = $this->getFlyerById($request->getPathParam('id'));
        if (!$flyer) {
            return $this->getResponseJson400('no such flyer');
        }

        $data = $request->getData();
        if (empty($data)) {
            return $this->getResponseJson400('Request is empty');
        }

        $form = new FlyerUpdateForm($data);
        if (!$form->isValid()) {
            return $this->getResponseJson400($form->getErrors());
        }

        $this->services->get(Flyer::ID)->save(
            $form->fillFlyer($flyer)
        );

        return $this->getResponseJson200(null, 'Item updated.');
    }

    /**
     * API DELETE: /flyers/<id>
     */
    public function deleteAction(Request $request)
    {
        if (!$this->isAuthenticatedBasic($request)) {
            return $this->getResponseJson403();
        }

        $flyer = $this->getFlyerById($request->getPathParam('id'));
        if (!$flyer) {
            return $this->getResponseJson400('No such flyer.');
        }

        $this->services->get(Flyer::ID)->remove($flyer);

        return $this->getResponseJson200(null, 'Item deleted.');
    }

    /**
     * @param $id
     * @return \App\Entity\Flyer|null
     */
    protected function getFlyerById($id)
    {
        return $this->services->get(Flyer::ID)->find($id);
    }
}
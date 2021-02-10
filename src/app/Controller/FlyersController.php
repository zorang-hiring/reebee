<?php
declare(strict_types=1);

namespace App\Controller;

use App\Form\FlyerCreateForm;
use App\Form\FlyerSaveForm;
use App\Form\FlyerUpdateForm;
use App\Request;
use App\Service\Flyer;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="Search API", version="1.0.0")
 */
class FlyersController extends AbstractController
{
    /**
     * @OA\Get(
     *     path="/flyers",
     *     @OA\Response(
     *         response="200",
     *         description="Get all"
     *     )
     * )
     */
    public function indexAction(Request $request)
    {

        return $this->getResponseJson200(
            $this->services->get(Flyer::ID)->findAllValid()
        );
    }

    public function getAction(Request $request)
    {
        return $this->getResponseJson200(
            $this->getFlyerById($request->getPathParam('id'))
        );
    }

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

        return $this->getResponseJson201($flyer);
    }

    public function patchAction(Request $request)
    {
        if (!$this->isAuthenticatedBasic($request)) {
            return $this->getResponseJson403();
        }

        $flyer = $this->getFlyerById($request->getPathParam('id'));
        if (!$flyer) {
            return $this->getResponseJson400('no such flyer');
        }

        $form = new FlyerUpdateForm($request->getData());
        if (!$form->isValid()) {
            return $this->getResponseJson400($form->getErrors());
        }

        $this->services->get(Flyer::ID)->save(
            $form->fillFlyer($flyer)
        );

        return $this->getResponseJson204();
    }

    public function deleteAction(Request $request)
    {
        if (!$this->isAuthenticatedBasic($request)) {
            return $this->getResponseJson403();
        }

        $flyer = $this->getFlyerById($request->getPathParam('id'));
        if (!$flyer) {
            return $this->getResponseJson400('no such flyer');
        }

        $this->services->get(Flyer::ID)->remove($flyer);

        return $this->getResponseJson204();
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
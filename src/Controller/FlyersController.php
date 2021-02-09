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
        return $this->newResponseJson(
            $this->services->get(Flyer::ID)->findAllValid(),
            200
        );
    }

    public function getAction(Request $request)
    {
        return $this->newResponseJson(
            $this->getFlyerById($request->getPathParam('id')),
            200
        );
    }

    public function postAction(Request $request)
    {
        if (!$this->isAuthenticatedBasic($request)) {
            return $this->newResponseJson([], 403);
        }

        $form = new FlyerCreateForm($request->getPostData());
        if (!$form->isValid()) {
            return $this->newResponseJson(['errors' => $form->getErrors()], 400);
        }

        $flyer = new \App\Entity\Flyer();
        $this->services->get(Flyer::ID)->save(
            $form->fillFlyer($flyer)
        );

        return $this->newResponseJson($flyer, 201);
    }

    public function patchAction(Request $request)
    {
        if (!$this->isAuthenticatedBasic($request)) {
            return $this->newResponseJson([], 403);
        }

        $flyer = $this->getFlyerById($request->getPathParam('id'));
        if (!$flyer) {
            return $this->newResponseJson(['message' => 'no such flyer'], 400);
        }

        $form = new FlyerUpdateForm($request->getPostData());
        if (!$form->isValid()) {
            return $this->newResponseJson(['errors' => $form->getErrors()], 400);
        }

        $this->services->get(Flyer::ID)->save(
            $form->fillFlyer($flyer)
        );

        return $this->newResponseJson([], 204);
    }

    public function deleteAction(Request $request)
    {
        if (!$this->isAuthenticatedBasic($request)) {
            return $this->newResponseJson([], 403);
        }

        $flyer = $this->getFlyerById($request->getPathParam('id'));
        if (!$flyer) {
            return $this->newResponseJson(['message' => 'no such flyer'], 400);
        }

        $this->services->get(Flyer::ID)->remove($flyer);

        return $this->newResponseJson([], 204);
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
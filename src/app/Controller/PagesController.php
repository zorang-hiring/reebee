<?php
declare(strict_types=1);

namespace App\Controller;

use App\Form\FlyerCreateForm;
use App\Form\FlyerSaveForm;
use App\Form\FlyerUpdateForm;
use App\Request;
use App\Service\Flyer;
use App\Service\Page;
use OpenApi\Annotations as OA;

class PagesController extends AbstractController
{
    public function getAction(Request $request)
    {
        $page = $this->getPageById($request->getPathParam('id'));

        if (!$page) {
            return $this->getResponseJson400(['Page not found.']);
        }

        return $this->getResponseJson200($page);
    }

//    public function postAction(Request $request)
//    {
//        if (!$this->isAuthenticatedBasic($request)) {
//            return $this->getResponseJson403();
//        }
//
//        $form = new FlyerCreateForm($request->getData());
//        if (!$form->isValid()) {
//            return $this->getResponseJson400($form->getErrors());
//        }
//
//        $flyer = new \App\Entity\Flyer();
//        $this->services->get(Flyer::ID)->save(
//            $form->fillFlyer($flyer)
//        );
//
//        return $this->getResponseJson200($flyer);
//    }
//
//    public function patchAction(Request $request)
//    {
//        if (!$this->isAuthenticatedBasic($request)) {
//            return $this->getResponseJson403();
//        }
//
//        $flyer = $this->getFlyerById($request->getPathParam('id'));
//        if (!$flyer) {
//            return $this->getResponseJson400('no such flyer');
//        }
//
//        $data = $request->getData();
//        if (empty($data)) {
//            return $this->getResponseJson400('Request is empty');
//        }
//
//        $form = new FlyerUpdateForm($data);
//        if (!$form->isValid()) {
//            return $this->getResponseJson400($form->getErrors());
//        }
//
//        $this->services->get(Flyer::ID)->save(
//            $form->fillFlyer($flyer)
//        );
//
//        return $this->getResponseJson200(null, 'Item updated.');
//    }
//
//    public function deleteAction(Request $request)
//    {
//        if (!$this->isAuthenticatedBasic($request)) {
//            return $this->getResponseJson403();
//        }
//
//        $flyer = $this->getFlyerById($request->getPathParam('id'));
//        if (!$flyer) {
//            return $this->getResponseJson400('No such flyer.');
//        }
//
//        $this->services->get(Flyer::ID)->remove($flyer);
//
//        return $this->getResponseJson200(null, 'Item deleted.');
//    }

    /**
     * @param $id
     * @return \App\Entity\Page|null
     */
    protected function getPageById($id)
    {
        return $this->services->get(Page::ID)->find($id);
    }
}
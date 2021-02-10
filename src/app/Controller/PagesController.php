<?php
declare(strict_types=1);

namespace App\Controller;

use App\Form\FlyerSaveForm;
use App\Form\PageCreateForm;
use App\Form\PageUpdateForm;
use App\Request;
use App\Service\Flyer;
use App\Service\Page;

class PagesController extends AbstractController
{
    /**
     * API GET: /pages/<id>
     */
    public function getAction(Request $request)
    {
        $page = $this->getPageById($request->getPathParam('id'));

        if (!$page) {
            return $this->getResponseJson400(['Page not found.']);
        }

        return $this->getResponseJson200($page);
    }

    /**
     * API POST: /pages
     */
    public function postAction(Request $request)
    {
        if (!$this->isAuthenticatedBasic($request)) {
            return $this->getResponseJson403();
        }

        $form = new PageCreateForm($request->getData(), $this->services->get(Flyer::ID));
        if (!$form->isValid()) {
            return $this->getResponseJson400($form->getErrors());
        }

        $page = new \App\Entity\Page();
        $this->services->get(Page::ID)->save(
            $form->fillPage($page)
        );

        return $this->getResponseJson200($page);
    }

    /**
     * API PATCH: /pages/<id>
     */
    public function patchAction(Request $request)
    {
        if (!$this->isAuthenticatedBasic($request)) {
            return $this->getResponseJson403();
        }

        $page = $this->getPageById($request->getPathParam('id'));
        if (!$page) {
            return $this->getResponseJson400('no such page');
        }

        $data = $request->getData();
        if (empty($data)) {
            return $this->getResponseJson400('Request is empty');
        }

        $form = new PageUpdateForm($data);
        if (!$form->isValid()) {
            return $this->getResponseJson400($form->getErrors());
        }

        $this->services->get(Page::ID)->save(
            $form->fillPage($page)
        );

        return $this->getResponseJson200(null, 'Item updated.');
    }

    /**
     * API DELETE: /pages/<id>
     */
    public function deleteAction(Request $request)
    {
        if (!$this->isAuthenticatedBasic($request)) {
            return $this->getResponseJson403();
        }

        $page = $this->getPageById($request->getPathParam('id'));
        if (!$page) {
            return $this->getResponseJson400('No such page.');
        }

        $this->services->get(Page::ID)->remove($page);

        return $this->getResponseJson200(null, 'Item deleted.');
    }

    /**
     * @param $id
     * @return \App\Entity\Page|null
     */
    protected function getPageById($id)
    {
        return $this->services->get(Page::ID)->find($id);
    }
}
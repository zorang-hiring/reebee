<?php
declare(strict_types=1);

namespace App\Controller;

use App\Form\UserCreateForm;
use App\Request;
use App\Response;
use App\Service\User;

class UsersController extends AbstractController
{
    public function indexAction(Request $request)
    {
        if ($request->isPost()) {
            return $this->postAction($request);
        }

        return $this->newResponseJson('', 404);
    }

    /**
     * Create new user
     *
     * @param Request $request
     * @return Response
     */
    public function postAction(Request $request)
    {
        if (!$this->getAuthentication()->isAllowedToCreateUsers($request)) {
            return $this->newResponseJson('', 401);
        }

        /** @var User $userService */
        $userService = $this->services->get(User::ID);

        $form = new UserCreateForm(
            $request,
            $userService->getRepository()
        );

        if (!$form->isValid()) {
            return $this->newResponseJson(['errors' => $form->getErrors()], 400);
        }

        $userService->save(
            $user = $form->fillUser(new \App\Entity\User(null))
        );

        return $this->newResponseJson($user, 201);
    }
}
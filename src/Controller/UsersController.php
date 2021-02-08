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

        return (new Response())->setStatus(404);
    }

    /**
     * Create new user
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function postAction(Request $request)
    {
        if (!$this->getAuthentication()->isAllowedToCreateUsers($request)) {
            return (new Response())->setStatus(401);
        }

        /** @var User $userService */
        $userService = $this->services->get(User::ID);

        $form = new UserCreateForm(
            $request,
            $userService->getRepository()
        );

        if (!$form->isValid()) {
            return (new Response())
                ->setStatus(400)
                ->setBody(json_encode(['errors' => $form->getErrors()]));
        }

        // save user to db
        $userService->save(
            $user = $form->fillUser(new \App\Entity\User(null))
        );

        // save user to db
        return (new Response())
            ->setStatus(201)
            ->setBody(json_encode($user));
    }
}
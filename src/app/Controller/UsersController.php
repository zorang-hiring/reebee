<?php
declare(strict_types=1);

namespace App\Controller;

use App\Form\UserCreateForm;
use App\Request;
use App\Response;
use App\Service\User;

class UsersController extends AbstractController
{
    /**
     * Create new user
     *
     * @param Request $request
     * @return Response
     */
    public function postAction(Request $request)
    {
        if (!$this->getAuthentication()->isAllowedToCreateUsers($request)) {
            return $this->newResponseJson([
                'status' => 'ERROR',
                'errors' => 'Client is not authorised.'
            ], 401);
        }

        /** @var User $userService */
        $userService = $this->services->get(User::ID);

        $form = new UserCreateForm($request, $userService->getRepository());

        if (!$form->isValid()) {
            return $this->getResponseJson400($form->getErrors());
        }

        $userService->save(
            $user = $form->fillUser(new \App\Entity\User(null))
        );

        return $this->getResponseJson200($user);
    }
}
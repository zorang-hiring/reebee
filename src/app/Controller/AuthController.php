<?php
declare(strict_types=1);

namespace App\Controller;

use App\Form\UserCreateForm;
use App\Request;
use App\Response;
use App\Service\Auth;
use App\Service\User;

class AuthController extends AbstractController
{
    public function postAction(Request $request)
    {
        if (!$request->isPost()) {
            $this->newResponseJson('', 405);
        }

        /** @var Auth $authService */
        $authService = $this->services->get(Auth::ID);

        if ($user = $authService->findUserByCredentials($request, $request->getData()['password'])) {
            return $this->newResponseJson([
                'token' => $authService->generateBasicToken($user, $request->getData()['password'])
            ], 200);
        }

        return $this->getResponseJson400(['Wrong credentials provided.']);
    }
}
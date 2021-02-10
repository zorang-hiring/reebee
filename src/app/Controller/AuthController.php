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
            $this->newResponseJson(
                ['status'=>'error', 'message' => 'method not allowed'],
                405
            );
        }

        /** @var Auth $authService */
        $authService = $this->services->get(Auth::ID);

        $rData = $request->getData();

        if ($user = $authService->findUserByCredentials(
            $rData['username'] ?: null,
            $rData['password'] ?: null
        )) {
            return $this->newResponseJson([
                'status' => 'OK',
                'token' => $authService->generateBasicToken($user, $rData['password'])
            ], 200);
        }

        return $this->getResponseJson400(['Wrong credentials provided.']);
    }
}
<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use App\Request;

class Auth
{
    const ID = 'Auth';

    protected const APP_SECRET_KEY = 'APP_SECRET_KEY';
    protected const APP_CREATE_USERS_TOKEN = 'APP_CREATE_USERS_TOKEN';

    /**
     * @var UserRepositoryInterface
     */
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function isAllowedToCreateUsers(Request $request)
    {
        if (!$token = $request->getHeaderValue('Authorization')) {
            return false;
        }
        $token = explode(' ', $token);
        if (array_key_exists(1, $token)) {
            if ($token[1] === $request->getEnvVar(self::APP_CREATE_USERS_TOKEN)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Request $request
     * @return User|bool False if token is invalid
     */
    public function authenticateBasic(Request $request)
    {
        if (!$token = $request->getHeaderValue('Authorization')) {
            return false;
        }
        $token = explode(' ', $token);
        if (!isset($token[0]) || $token[0] !== 'Basic') {
            return false;
        }
        if (array_key_exists(1, $token)) {
            $decoded = base64_decode($token);
            list($username, $pass) = explode(':', $decoded);
            $user = $this->userRepository->authenticate(
                $username,
                self::encryptPassword($request, $pass)
            );
            if (!$user) {
                return false;
            }
            if ($user->getUsername()) {

            }
        }
        return false;
    }

    /**
     * @param User $user
     * @param string $password
     * @return string
     */
    public function generateBasicToken(User $user, $password)
    {
        return base64_encode($user->getUsername().':'.$password);
    }

    public static function encryptPassword(Request $request, $password)
    {
        return password_hash(
            $password . $request->getEnvVar(self::APP_SECRET_KEY),
            PASSWORD_DEFAULT
        );
    }
}
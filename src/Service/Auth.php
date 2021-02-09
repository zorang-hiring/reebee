<?php
declare(strict_types=1);

namespace App\Service;

use App\App;
use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use App\Request;

class Auth
{
    const ID = 'Auth';

    const APP_SECRET_KEY = 'APP_SECRET_KEY';
    const APP_CREATE_USERS_TOKEN = 'APP_CREATE_USERS_TOKEN';

    /**
     * @var User|null|bool
     */
    protected $authenticated = null;

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
            if ($token[1] === App::getEnv(self::APP_CREATE_USERS_TOKEN)) {
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
        if ($this->authenticated !== null) {
            return $this->authenticated;
        }
        if (!$token = $request->getHeaderValue('Authorization')) {
            return $this->authenticated = false;
        }
        $token = explode(' ', $token);
        // check Authorization type
        if (!isset($token[0]) || $token[0] !== 'Basic') {
            return $this->authenticated = false;
        }
        // check if Authorization exists
        if (!array_key_exists(1, $token)) {
            return $this->authenticated = false;
        }

        // check Authorization token
        $decoded = base64_decode($token);
        list($username, $pass) = explode(':', $decoded);
        $user = $this->findUserByCredentials($username, $pass);
        if (!$user) {
            return $this->authenticated =false;
        }

        $this->authenticated = $user;
    }

    /**
     * @param $username
     * @param $pass
     * @return User|null
     */
    public function findUserByCredentials($username, $pass)
    {
        return $this->userRepository->findUserByCredentials($username, self::encryptPassword($pass));
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

    public static function encryptPassword($password)
    {
        return password_hash(
            $password . App::getEnv(self::APP_SECRET_KEY),
            PASSWORD_DEFAULT
        );
    }
}
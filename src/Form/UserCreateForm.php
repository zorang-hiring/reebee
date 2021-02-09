<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use App\Repository\DbConnectorInterface;
use App\Repository\UserRepositoryInterface;
use App\Request;
use App\Service\Auth;

class UserCreateForm extends AbstractForm
{
    protected $username;

    protected $password;

    /**
     * @var UserRepositoryInterface
     */
    protected $userRepository;

    public function __construct(Request $request, UserRepositoryInterface $userRepository)
    {
        $data = $request->getPostData();
        $this->username = $data['username'];
        $this->password = $data['password'];
        $this->userRepository = $userRepository;
    }

    public function isValid()
    {
        if (strlen($this->username) === 0 || strlen($this->username) > 255) {
            $this->addError('username', 'Has to be between 0 and 255 characters.');
        }
        if (strlen($this->password) <= 3 || strlen($this->password) > 20) {
            $this->addError('password', 'Has to be between 3 and 20 characters.');
        }
        if (empty($this->errors['username'])) {
            if ($this->userRepository->findOneByUsername($this->username)){
                $this->addError('username', 'Username already exists.');
            }
        }

        return empty($this->getErrors());
    }

    public function fillUser(User $user)
    {
        $user->setUsername($this->username);
        $user->setPassword(Auth::encryptPassword($this->password));
        return $user;
    }
}
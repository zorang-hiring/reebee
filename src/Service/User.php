<?php
declare(strict_types=1);

namespace App\Service;

use App\Repository\UserRepositoryInterface;

class User
{
    const ID = 'User';

    /**
     * @var UserRepositoryInterface
     */
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function save(\App\Entity\User $user)
    {
        $this->userRepository->save($user);
    }
}
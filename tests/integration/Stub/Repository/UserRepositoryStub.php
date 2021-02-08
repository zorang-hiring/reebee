<?php
declare(strict_types=1);

namespace Tests\Integration\Stub\Repository;

use App\Entity\User;
use App\Repository\UserRepositoryInterface;

class UserRepositoryStub implements UserRepositoryInterface
{
    public function authenticate($username, $encryptedPassword)
    {
        return false;
    }

    public function save(User $user)
    {
        // TODO: Implement save() method.
    }
}
<?php
declare(strict_types=1);

namespace Tests\Integration\Stub\Repository;

use App\Repository\UserRepositoryInterface;

class UserRepositoryStub implements UserRepositoryInterface
{
    public function authenticate($username, $encryptedPassword)
    {
        return false;
    }
}
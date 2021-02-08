<?php
declare(strict_types=1);

namespace Tests\Integration\Stub\Repository;

use App\Entity\User;
use App\Repository\UserRepositoryInterface;

class UserRepositoryStub implements UserRepositoryInterface
{
    protected $savedData = [];

    public function authenticate($username, $encryptedPassword)
    {
        return false;
    }

    public function save($username, $encryptedPassword)
    {
        $this->savedData[] = ['username' => $username, 'password' => $encryptedPassword];
    }

    public function findOneByUsername($username)
    {

    }

    public function getSavedData()
    {
        return $this->savedData;
    }
}
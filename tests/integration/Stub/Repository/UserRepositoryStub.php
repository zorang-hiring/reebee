<?php
declare(strict_types=1);

namespace Tests\Integration\Stub\Repository;

use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class UserRepositoryStub implements UserRepositoryInterface
{
    protected $savedData = [];

    public function isValidCredentials($username, $encryptedPassword)
    {
        return false;
    }

    public function save(User $user)
    {
        $this->savedData[] = $user;
    }

    public function findOneByUsername($username)
    {

    }

    public function getSavedData()
    {
        return $this->savedData;
    }
}
<?php
declare(strict_types=1);

namespace Tests\Integration\Stub\Repository;

use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class UserRepositoryStub implements UserRepositoryInterface
{
    protected $savedData = [];

    /**
     * @var User[]
     */
    protected $findUserByCredentialsData = [];

    public function findUserByCredentials($username, $encryptedPassword)
    {
        foreach ($this->findUserByCredentialsData as $user) {
            if ($user->getUsername() === $username) {
                return $user;
            }
        }
        return false;
    }

    /**
     * @param User[] $findUserByCredentialsData
     */
    public function setFindUserByCredentialsData(array $findUserByCredentialsData)
    {
        $this->findUserByCredentialsData = $findUserByCredentialsData;
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
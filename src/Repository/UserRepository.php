<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository implements UserRepositoryInterface
{
    /**
     * @param string $username
     * @param string $encryptedPassword
     * @return boolean
     */
    public function isValidCredentials($username, $encryptedPassword)
    {
        return !!$this->findOneBy(['username' => $username, 'password' => $encryptedPassword]);
    }

    /**
     * @param User $user
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(User $user)
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush($user);
    }

    /**
     * @param $username
     * @return User
     */
    public function findOneByUsername($username)
    {
        return $this->findOneBy(['username' => $username]);
    }
}
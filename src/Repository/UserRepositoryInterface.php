<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;

interface UserRepositoryInterface
{
    /**
     * @param string $username
     * @param string $encryptedPassword
     * @return User|null Null if no user or wrong password
     */
    public function authenticate($username, $encryptedPassword);

    public function save($username, $encryptedPassword);

    public function findOneByUsername($username);
}
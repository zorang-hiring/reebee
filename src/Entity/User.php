<?php
declare(strict_types=1);

namespace App\Entity;

class User
{
//    CONST ROLE_ADMIN = 'admin';
//    CONST ROLE_MEMBER = 'member';

    /**
     * @var string
     */
    protected $username;

//    protected $role;

    public function __construct($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }
}
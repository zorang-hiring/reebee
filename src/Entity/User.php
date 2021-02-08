<?php
declare(strict_types=1);

namespace App\Entity;

class User
{
    /**
     * @var string
     */
    protected $username;

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
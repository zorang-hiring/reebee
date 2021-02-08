<?php
declare(strict_types=1);

namespace App\Entity;

class User implements \JsonSerializable
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
     * @param string $username
     */
    public function setUsername(string $username)
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

    public function jsonSerialize()
    {
        return [
            'username' => $this->username
        ];
    }
}
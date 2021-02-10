<?php
declare(strict_types=1);

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table (name="users")
 */
class User implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\Column (type="integer")
     * @ORM\GeneratedValue
     * @var integer
     */
    protected $id;

    /**
     * @ORM\Column(type="string", unique=true, nullable=false)
     *
     * @var string
     */
    protected $username;

    /**
     * @ORM\Column(type="string", nullable=false)
     *
     * @var string
     */
    protected $password;

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
     * @param string $username
     */
    public function setPassword(string $username)
    {
        $this->password = $username;
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
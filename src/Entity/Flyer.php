<?php
declare(strict_types=1);

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FlyerRepository")
 * @ORM\Table (name="flyers")
 */
class Flyer implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\Column (type="integer")
     * @ORM\GeneratedValue
     * @var integer
     *
     * @var Integer
     */
    protected $flyerID;

    /**
     * The customer's description of the flyer
     *
     * @ORM\Column(type="string", unique=true, nullable=false)
     *
     * @var string
     */
    protected $name;

    /**
     * The name of the customer's business. (This is reused *a lot*).
     *
     * @ORM\Column(type="string", unique=true, nullable=false)
     *
     * @var string
     */
    protected $storeName;

    /**
     * The date flyer becomes valid
     *
     * @ORM\Column(type="date", unique=true, nullable=false)
     *
     * @var \DateTime
     */
    protected $dateValid;

    /**
     * The date that the flyer expires
     *
     * @ORM\Column(type="date", unique=true, nullable=false)
     *
     * @var \DateTime
     */
    protected $dateExpired;

    /**
     * The number of pages in the flyer
     *
     * @ORM\Column(type="integer", unique=true, nullable=false)
     *
     * @var Integer
     */
    protected $pageCount;

    /**
     * @return mixed
     */
    public function getFlyerID()
    {
        return $this->flyerID;
    }

    /**
     * @param mixed $flyerID
     * @return self
     */
    public function setFlyerID($flyerID)
    {
        $this->flyerID = $flyerID;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return self
     */
    public function setName(string $name): Flyer
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getStoreName(): string
    {
        return $this->storeName;
    }

    /**
     * @param string $storeName
     * @return self
     */
    public function setStoreName(string $storeName): Flyer
    {
        $this->storeName = $storeName;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateValid(): \DateTime
    {
        return $this->dateValid;
    }

    /**
     * @param \DateTime $dateValid
     * @return self
     */
    public function setDateValid(\DateTime $dateValid): Flyer
    {
        $this->dateValid = $dateValid;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateExpired(): \DateTime
    {
        return $this->dateExpired;
    }

    /**
     * @param \DateTime $dateExpired
     * @return self
     */
    public function setDateExpired(\DateTime $dateExpired): Flyer
    {
        $this->dateExpired = $dateExpired;
        return $this;
    }

    /**
     * @return int
     */
    public function getPageCount(): int
    {
        return $this->pageCount;
    }

    /**
     * @param int $pageCount
     * @return self
     */
    public function setPageCount(int $pageCount): Flyer
    {
        $this->pageCount = $pageCount;
        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'flyerID' => $this->getFlyerID(),
            'name' => $this->getName(),
            'storeName' => $this->getStoreName(),
            'dateValid' => $this->getDateValid()->format('Y-m-d'),
            'dateExpired' => $this->getDateExpired()->format('Y-m-d'),
            'pageCount' => $this->getPageCount(),
        ];
    }
}
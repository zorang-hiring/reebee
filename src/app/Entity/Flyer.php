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
     *
     * @var Integer
     */
    protected $flyerID;

    /**
     * The customer's description of the flyer
     *
     * @ORM\Column(type="string", nullable=false)
     *
     * @var string
     */
    protected $name;

    /**
     * The name of the customer's business. (This is reused *a lot*).
     *
     * @ORM\Column(type="string", nullable=false)
     *
     * @var string
     */
    protected $storeName;

    /**
     * The date flyer becomes valid
     *
     * @ORM\Column(type="date", nullable=false)
     *
     * @var \DateTime
     */
    protected $dateValid;

    /**
     * The date that the flyer expires
     *
     * @ORM\Column(type="date", nullable=false)
     *
     * @var \DateTime
     */
    protected $dateExpired;

    /**
     * @todo remove since pages will be read automatically
     *
     * The number of pages in the flyer
     *
     * @ORM\Column(type="integer", nullable=false)
     *
     * @var Integer
     */
    protected $pageCount;

    /**
     * @ORM\OneToMany(targetEntity="Page", mappedBy="flyer")
     *
     * @var Page[]
     */
    protected $pages;

    protected function __construct()
    {
        $this->pages = [];
    }

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return self
     */
    public function setName($name): Flyer
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getStoreName()
    {
        return $this->storeName;
    }

    /**
     * @param string $storeName
     * @return self
     */
    public function setStoreName($storeName): Flyer
    {
        $this->storeName = $storeName;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateValid()
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
    public function getDateExpired()
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
    public function getPageCount()
    {
        return $this->pageCount;
    }

    /**
     * @todo remove since pages will be read automatically
     *
     * @param int $pageCount
     * @return self
     */
    public function setPageCount($pageCount): Flyer
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
            'dateValid' => $this->getDateValid() ? $this->getDateValid()->format('Y-m-d') : null,
            'dateExpired' => $this->getDateExpired() ? $this->getDateExpired()->format('Y-m-d') : null,
            'pageCount' => $this->getPageCount(),
        ];
    }
}
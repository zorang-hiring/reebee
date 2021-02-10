<?php
declare(strict_types=1);

namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
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
     * @ORM\OneToMany(targetEntity="Page", mappedBy="flyer")
     *
     * @var Page[]
     */
    protected $pages;

    public function __construct()
    {
        $this->pages = new ArrayCollection();
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
        return count($this->pages);
    }

    /**
     * @return ArrayCollection|Page[]
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * @param Page $page
     * @return self
     */
    public function addPage(Page $page)
    {
        $this->pages->add($page);
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
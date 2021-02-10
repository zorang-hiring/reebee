<?php
declare(strict_types=1);

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PageRepository")
 * @ORM\Table (name="pages")
 */
class Page implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @var string
     */
    protected $pageID;

    /**
     * @ORM\ManyToOne(targetEntity="Flyer", inversedBy="pages")
     * @ORM\JoinColumn(name="flyerID", referencedColumnName="flyerID", nullable=false)
     *
     * @var Flyer
     */
    protected $flyer;

    /**
     * The date page becomes valid
     *
     * @ORM\Column(type="date", nullable=false)
     *
     * @var \DateTime
     */
    protected $dateValid;

    /**
     * The date that the page expires
     *
     * @ORM\Column(type="date", nullable=false)
     *
     * @var \DateTime
     */
    protected $dateExpired;

    /**
     * The numeric order that the page appears in the flyer
     *
     * @ORM\Column(type="integer", nullable=false)
     *
     * @var integer
     */
    protected $pageNumber;

    /**
     * @return string
     */
    public function getPageID()
    {
        return $this->pageID;
    }

    /**
     * @param string $pageID
     * @return self
     */
    public function setPageID($pageID)
    {
        $this->pageID = $pageID;
        return $this;
    }

    /**
     * @return integer
     */
    public function getPageNumber()
    {
        return $this->pageNumber;
    }

    /**
     * @param integer $pageNumber
     * @return self
     */
    public function setPageNumber($pageNumber)
    {
        $this->pageNumber = $pageNumber;
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
    public function setDateValid(\DateTime $dateValid)
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
    public function setDateExpired(\DateTime $dateExpired)
    {
        $this->dateExpired = $dateExpired;
        return $this;
    }

    /**
     * @param Flyer $flyer
     * @return self
     */
    public function setFlyer(Flyer $flyer)
    {
        $this->flyer = $flyer;
        return $this;
    }

    /**
     * @return Flyer
     */
    public function getFlyer()
    {
        return $this->flyer;
    }

    public function jsonSerialize()
    {
        return [
            'pageID' => $this->getPageID(),
            'dateValid' => $this->getDateValid() ? $this->getDateValid()->format('Y-m-d') : null,
            'dateExpired' => $this->getDateExpired() ? $this->getDateExpired()->format('Y-m-d') : null,
            'pageNumber' => $this->getPageNumber(),
        ];
    }
}
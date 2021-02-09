<?php
declare(strict_types=1);

namespace App\Service;

use App\Repository\FlyerRepositoryInterface;

class Flyer
{
    const ID = 'Flyer';

    /**
     * @var FlyerRepositoryInterface
     */
    protected $flyerRepository;

    public function __construct(FlyerRepositoryInterface $flyerRepository)
    {
        $this->flyerRepository = $flyerRepository;
    }

    /**
     * @return \App\Entity\Flyer[]
     */
    public function findAllValid()
    {
        return $this->flyerRepository->findAllValid();
    }

    /**
     * @param integer $id
     * @return \App\Entity\Flyer
     */
    public function find($id)
    {
        return $this->flyerRepository->findOne($id);
    }
}
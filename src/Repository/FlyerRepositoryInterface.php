<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Flyer;

interface FlyerRepositoryInterface
{
    /**
     * @return Flyer[]
     */
    public function findAllValid();

    /**
     * @param integer $id
     * @return Flyer
     */
    public function findOne($id);
}
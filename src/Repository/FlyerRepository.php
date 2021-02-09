<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Flyer;
use Doctrine\ORM\EntityRepository;
use Carbon\Carbon;

class FlyerRepository extends EntityRepository implements FlyerRepositoryInterface
{
    /**
     * @return Flyer[]
     */
    public function findAllValid()
    {
        return $this->getEntityManager()->createQueryBuilder('f')
            ->where('f.dateValid >= :now')
            ->andWhere('f.dateExpired < :now')
            ->setParameter('now', Carbon::now())
            ->getQuery()
            ->getResult();
    }

    /**
     * @param integer $id
     * @return Flyer
     */
    public function findOne($id)
    {
        return $this->find($id);
    }
}
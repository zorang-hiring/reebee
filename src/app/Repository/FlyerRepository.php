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
            ->select(['f'])
            ->from(Flyer::class, 'f')
            ->where('f.dateValid <= :now')
            ->andWhere('f.dateExpired > :now')
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

    /**
     * @param Flyer $flyer
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Flyer $flyer)
    {
        $this->getEntityManager()->persist($flyer);
        $this->getEntityManager()->flush($flyer);
    }

    /**
     * @param Flyer $flyer
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove(Flyer $flyer)
    {
        $this->getEntityManager()->remove($flyer);
        $this->getEntityManager()->flush($flyer);
    }
}
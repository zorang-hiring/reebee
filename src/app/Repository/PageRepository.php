<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Page;
use Doctrine\ORM\EntityRepository;

class PageRepository extends EntityRepository implements PageRepositoryInterface
{
    /**
     * @param integer $id
     * @return Page
     */
    public function findOne($id)
    {
        return $this->find($id);
    }

    /**
     * @param Page $page
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Page $page)
    {
        $this->getEntityManager()->persist($page);
        $this->getEntityManager()->flush($page);
    }

    /**
     * @param Page $page
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove(Page $page)
    {
        $this->getEntityManager()->remove($page);
        $this->getEntityManager()->flush($page);
    }
}
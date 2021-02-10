<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Flyer;
use App\Entity\Page;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

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

    /**
     * @param Flyer $flyer
     * @return int
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getMaxPageNumberByFlyer(Flyer $flyer)
    {
        $sql = sprintf(
            'select MAX(pageNumber) AS maxPageNumber from pages where flyerID = "%s"',
            $flyer->getFlyerID()
        );

        $statement = $this->getEntityManager()->getConnection()->prepare($sql);
        $statement->execute();
        $result = $statement->fetch();

        if (array_key_exists('maxPageNumber', $result)) {
            return $result['maxPageNumber'] + 1;
        }
        return 0;
    }
}
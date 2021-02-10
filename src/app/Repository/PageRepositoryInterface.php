<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Page;

interface PageRepositoryInterface
{
    /**
     * @param integer $id
     * @return Page
     */
    public function findOne($id);

    /**
     * @param Page $page
     */
    public function save(Page $page);

    /**
     * @param Page $page
     */
    public function remove(Page $page);
}
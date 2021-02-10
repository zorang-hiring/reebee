<?php
declare(strict_types=1);

namespace App\Service;

use App\Repository\FlyerRepositoryInterface;
use App\Repository\PageRepositoryInterface;

class Page
{
    const ID = 'Page';

    /**
     * @var PageRepositoryInterface
     */
    protected $pageRepository;

    public function __construct(FlyerRepositoryInterface $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    /**
     * @param integer $id
     * @return \App\Entity\Page
     */
    public function find($id)
    {
        return $this->pageRepository->findOne($id);
    }

    public function save(\App\Entity\Page $page)
    {
        $this->pageRepository->save($page);
    }

    public function remove(\App\Entity\Page $page)
    {
        $this->pageRepository->remove($page);
    }
}
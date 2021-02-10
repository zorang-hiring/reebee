<?php
declare(strict_types=1);

namespace App\Subscriber;

use App\Repository\PageRepositoryInterface;
use Doctrine\ORM\Events;
use App\Entity\Page;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class PageSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return array(
            Events::prePersist,
        );
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        $entityManager = $args->getObjectManager();

        // on Page insert, calculate PageNumber
        if ($entity instanceof Page) {
            if (!$entity->getPageID()) {
                /** @var PageRepositoryInterface $repo */
                $repo = $entityManager->getRepository(Page::class);

                $entity->setPageNumber(
                    $repo->getMaxPageNumberByFlyer($entity->getFlyer())
                );
            }
        }
    }
}
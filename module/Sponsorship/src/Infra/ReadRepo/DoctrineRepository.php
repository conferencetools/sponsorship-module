<?php

namespace ConferenceTools\Sponsorship\Infra\ReadRepo;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineRepository implements Selectable //implements ReadRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    private $entity;

    public function __construct($entity, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function add($element)
    {
        $this->entityManager->persist($element);
    }

    public function remove($element)
    {
        $this->entityManager->remove($element);
    }

    public function get($key)
    {
        $this->entityManager->getRepository($this->entity)->find($key);
    }

    public function matching(Criteria $criteria)
    {
        /** @var \Doctrine\ORM\EntityRepository $repository*/
        $repository = $this->entityManager->getRepository($this->entity);
        return $repository->matching($criteria);
    }

    public function commit()
    {
        $this->entityManager->flush();
    }
}
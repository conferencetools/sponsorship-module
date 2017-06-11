<?php

namespace ConferenceTools\Sponsorship\Controller;

use Carnage\Cqrs\MessageBus\MessageBusInterface;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * @method \Carnage\Cqrs\Mvc\Controller\Plugin\Events events()
 */
abstract class AbstractController extends AbstractActionController
{
    /**
     * @var MessageBusInterface
     */
    private $commandBus;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * AbstractController constructor.
     * @param MessageBusInterface $commandBus
     * @param EntityManager $entityManager
     */
    public function __construct(
        MessageBusInterface $commandBus,
        EntityManager $entityManager
    ) {
        $this->commandBus = $commandBus;
        $this->entityManager = $entityManager;
    }

    /**
     * @return MessageBusInterface
     */
    public function getCommandBus()
    {
        return $this->commandBus;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }
}
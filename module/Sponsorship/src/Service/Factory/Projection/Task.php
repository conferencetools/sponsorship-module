<?php

namespace ConferenceTools\Sponsorship\Service\Factory\Projection;

use ConferenceTools\Sponsorship\Domain\Projection\Task as TaskProjection;
use ConferenceTools\Sponsorship\Domain\ReadModel\Task\Task as TaskEntity;
use ConferenceTools\Sponsorship\Infra\ReadRepo\DoctrineRepository;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Task implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator->getServiceLocator(), Task::class);
    }

    public function __invoke(ContainerInterface $container, $name, $options = [])
    {
        $entityManager = $container->get(EntityManager::class);
        $repository = new DoctrineRepository(TaskEntity::class, $entityManager);

        return new TaskProjection($repository);
    }
}
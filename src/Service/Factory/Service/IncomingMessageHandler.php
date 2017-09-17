<?php

namespace ConferenceTools\Sponsorship\Service\Factory\Service;

use Carnage\Cqorms\Persistence\ReadModel\DoctrineRepository;
use Carnage\Cqrs\Command\CommandBusInterface;
use ConferenceTools\Sponsorship\Domain\ReadModel\Mapping\Mapping;
use ConferenceTools\Sponsorship\Domain\Service\IncomingMessageHandler as IncomingMessageHandlerService;
use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class IncomingMessageHandler implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        return $this($serviceLocator, $requestedName);
    }

    public function __invoke(ContainerInterface $container, $name, $options = [])
    {
        $entityManager = $container->get(EntityManager::class);
        return new IncomingMessageHandlerService(
            $container->get(CommandBusInterface::class),
            new DoctrineRepository(Mapping::class, $entityManager)
        );
    }
}
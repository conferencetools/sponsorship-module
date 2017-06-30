<?php

namespace ConferenceTools\Sponsorship\Service\Factory\Service;

use Carnage\Cqorms\Persistence\ReadModel\DoctrineRepository;
use Carnage\Cqrs\Command\CommandBusInterface;
use ConferenceTools\Sponsorship\Domain\ReadModel\Conversation\Conversation;
use ConferenceTools\Sponsorship\Domain\Service\IncomingMessageHandler as IncomingMessageHandlerService;
use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class IncomingMessageHandler implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $entityManager = $serviceLocator->get(EntityManager::class);
        return new IncomingMessageHandlerService(
            $serviceLocator->get(CommandBusInterface::class),
            new DoctrineRepository(Conversation::class, $entityManager)
        );
    }
}
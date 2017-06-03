<?php

namespace ConferenceTools\Sponsorship\Cli\Command;

use Carnage\Cqrs\Command\CommandBusInterface;
use ConferenceTools\Sponsorship\Domain\ReadModel\Conversation\Conversation;
use ConferenceTools\Sponsorship\Infra\ReadRepo\DoctrineRepository;
use ConferenceTools\Sponsorship\Service\Mailgun\Client;
use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RetrieveEventsFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceLocator = $serviceLocator->getServiceLocator();
        $entityManager = $serviceLocator->get(EntityManager::class);
        return RetrieveEvents::build(
            $serviceLocator->get(CommandBusInterface::class),
            new DoctrineRepository(Conversation::class, $entityManager),
            $serviceLocator->get(Client::class)
        );
    }
}

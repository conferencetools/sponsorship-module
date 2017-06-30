<?php

namespace ConferenceTools\Sponsorship\Cli\Command;

use Carnage\Cqorms\Persistence\ReadModel\DoctrineRepository;
use Carnage\Cqrs\Command\CommandBusInterface;
use ConferenceTools\Sponsorship\Domain\ReadModel\Conversation\Conversation;
use ConferenceTools\Sponsorship\Domain\Service\IncomingMessageHandler;
use ConferenceTools\Sponsorship\Service\Mailgun\Client;
use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RetrieveEventsFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceLocator = $serviceLocator->getServiceLocator();
        return RetrieveEvents::build(
            $serviceLocator->get(IncomingMessageHandler::class),
            $serviceLocator->get(Client::class)
        );
    }
}

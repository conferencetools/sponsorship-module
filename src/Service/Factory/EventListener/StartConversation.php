<?php

namespace ConferenceTools\Sponsorship\Service\Factory\EventListener;

use Carnage\Cqrs\Command\CommandBusInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ConferenceTools\Sponsorship\Domain\EventListener\StartConversation as StartConversationListener;

class StartConversation implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator->getServiceLocator(), StartConversationListener::class);
    }

    public function __invoke(ContainerInterface $container, $name, $options = [])
    {
        return new StartConversationListener($container->get(CommandBusInterface::class));
    }
}
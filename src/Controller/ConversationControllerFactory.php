<?php
namespace ConferenceTools\Sponsorship\Controller;

use BsbFlysystem\Service\FilesystemManager;
use Carnage\Cqrs\Command\CommandBusInterface;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ConversationControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        return $this($serviceLocator->getServiceLocator(), $requestedName);
    }

    public function __invoke(ContainerInterface $container, $name, $options = [])
    {
        return new $name(
            $container->get(CommandBusInterface::class),
            $container->get(EntityManager::class),
            $container->get(FilesystemManager::class)->get('default')
        );
    }
}
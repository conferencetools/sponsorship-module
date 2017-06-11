<?php
namespace ConferenceTools\Sponsorship\Service\Factory;

use Carnage\Cqrs\Command\CommandBusInterface;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        return $this($serviceLocator->getServiceLocator(), $requestedName);
    }

    public function __invoke(ContainerInterface $container, $name, $options = [])
    {
        return new $name(
            $container->get(CommandBusInterface::class),
            $container->get(EntityManager::class)
        );
    }
}
<?php

namespace ConferenceTools\Sponsorship\Service\Factory\CommandHandler;

use Carnage\Cqrs\Aggregate\Identity\YouTubeStyleIdentityGenerator;
use ConferenceTools\Sponsorship\Domain\Model\AlarmClock as AlarmClockAggregate;
use ConferenceTools\Sponsorship\Domain\CommandHandler\AlarmClock as AlarmClockCommandHandler;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Carnage\Cqrs\Persistence\Repository\PluginManager as RepositoryManager;

class AlarmClock implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator->getServiceLocator(), AlarmClockCommandHandler::class);
    }

    public function __invoke(ContainerInterface $container, $name, $options = [])
    {
        $repositoryManager = $container->get(RepositoryManager::class);
        return new AlarmClockCommandHandler(
            $repositoryManager->get(AlarmClockAggregate::class),
            new YouTubeStyleIdentityGenerator()
        );
    }
}
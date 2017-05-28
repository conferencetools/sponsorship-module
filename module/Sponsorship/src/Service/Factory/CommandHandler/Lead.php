<?php

namespace ConferenceTools\Sponsorship\Service\Factory\CommandHandler;

use Carnage\Cqrs\Aggregate\Identity\YouTubeStyleIdentityGenerator;
use ConferenceTools\Sponsorship\Domain\Model\Lead\Lead as LeadAggregate;
use ConferenceTools\Sponsorship\Domain\CommandHandler\Lead as LeadCommandHandler;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Carnage\Cqrs\Persistence\Repository\PluginManager as RepositoryManager;

class Lead implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator->getServiceLocator(), LeadCommandHandler::class);
    }

    public function __invoke(ContainerInterface $container, $name, $options = [])
    {
        $repositoryManager = $container->get(RepositoryManager::class);
        new LeadCommandHandler(
            $repositoryManager->get(LeadAggregate::class),
            new YouTubeStyleIdentityGenerator()
        );
    }
}
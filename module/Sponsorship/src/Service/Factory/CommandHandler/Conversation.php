<?php

namespace ConferenceTools\Sponsorship\Service\Factory\CommandHandler;

use Carnage\Cqrs\Aggregate\Identity\YouTubeStyleIdentityGenerator;
use ConferenceTools\Sponsorship\Domain\Model\Conversation\Conversation as ConversationAggregate;
use ConferenceTools\Sponsorship\Domain\CommandHandler\Conversation as ConversationCommandHandler;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Carnage\Cqrs\Persistence\Repository\PluginManager as RepositoryManager;

class Conversation implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator->getServiceLocator(), ConversationCommandHandler::class);
    }

    public function __invoke(ContainerInterface $container, $name, $options = [])
    {
        $repositoryManager = $container->get(RepositoryManager::class);
        new ConversationCommandHandler(
            $repositoryManager->get(ConversationAggregate::class),
            new YouTubeStyleIdentityGenerator()
        );
    }
}
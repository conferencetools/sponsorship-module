<?php

namespace ConferenceTools\Sponsorship\Service\Factory\ProcessManager;

use ConferenceTools\Sponsorship\Domain\Process\Conversation as ConversationProcess;
use ConferenceTools\Sponsorship\Domain\ProcessManager\Conversation as ConversationProcessManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Carnage\Cqrs\Persistence\Repository\PluginManager as RepositoryManager;

class Conversation implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator->getServiceLocator(), ConversationProcessManager::class);
    }

    public function __invoke(ContainerInterface $container, $name, $options = [])
    {
        $repositoryManager = $container->get(RepositoryManager::class);
        return new ConversationProcessManager($repositoryManager->get(ConversationProcess::class));
    }
}
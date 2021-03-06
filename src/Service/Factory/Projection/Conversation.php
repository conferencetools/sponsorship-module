<?php

namespace ConferenceTools\Sponsorship\Service\Factory\Projection;

use Carnage\Cqorms\Persistence\ReadModel\DoctrineRepository;
use ConferenceTools\Sponsorship\Domain\Projection\Conversation as ConversationProjection;
use ConferenceTools\Sponsorship\Domain\ReadModel\Conversation\Conversation as ConversationEntity;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Conversation implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator->getServiceLocator(), ConversationProjection::class);
    }

    public function __invoke(ContainerInterface $container, $name, $options = [])
    {
        $entityManager = $container->get(EntityManager::class);
        $repository = new DoctrineRepository(ConversationEntity::class, $entityManager);

        return new ConversationProjection($repository);
    }
}
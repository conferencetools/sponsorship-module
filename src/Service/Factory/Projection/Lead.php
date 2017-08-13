<?php

namespace ConferenceTools\Sponsorship\Service\Factory\Projection;

use Carnage\Cqorms\Persistence\ReadModel\DoctrineRepository;
use ConferenceTools\Sponsorship\Domain\Projection\Lead as LeadProjection;
use ConferenceTools\Sponsorship\Domain\ReadModel\Lead\Lead as LeadEntity;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Lead implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator->getServiceLocator(), LeadProjection::class);
    }

    public function __invoke(ContainerInterface $container, $name, $options = [])
    {
        $entityManager = $container->get(EntityManager::class);
        $repository = new DoctrineRepository(LeadEntity::class, $entityManager);

        return new LeadProjection($repository);
    }
}
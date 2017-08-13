<?php


namespace ConferenceTools\Sponsorship\Service\Factory\Projection;

use Carnage\Cqorms\Persistence\ReadModel\DoctrineRepository;
use ConferenceTools\Sponsorship\Domain\Projection\Mapper as MapperProjection;
use ConferenceTools\Sponsorship\Domain\ReadModel\Mapping\Mapping;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Mapper implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator->getServiceLocator(), MapperProjection::class);
    }

    public function __invoke(ContainerInterface $container, $name, $options = [])
    {
        $entityManager = $container->get(EntityManager::class);
        $repository = new DoctrineRepository(Mapping::class, $entityManager);

        return new MapperProjection($repository);
    }
}
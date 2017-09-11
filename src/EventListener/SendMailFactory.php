<?php

namespace ConferenceTools\Sponsorship\EventListener;

use Carnage\Cqorms\Persistence\ReadModel\DoctrineRepository;
use ConferenceTools\Sponsorship\Domain\ReadModel\Mapping\Mapping;
use Doctrine\ORM\EntityManager;
use Zend\Mail\Transport\Factory;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SendMailFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceLocator = $serviceLocator->getServiceLocator();
        $config = $serviceLocator->get('Config');
        $transport = Factory::create($config['mail']);
        $entityManager = $serviceLocator->get(EntityManager::class);

        return new SendMail(
            new DoctrineRepository(Mapping::class, $entityManager),
            $serviceLocator->get('Zend\View\View'),
            $transport,
            $config['mailconf']['outbound'] ?? []
        );
    }
}

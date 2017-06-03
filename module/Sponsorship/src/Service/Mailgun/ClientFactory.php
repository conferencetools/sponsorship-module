<?php

namespace ConferenceTools\Sponsorship\Service\Mailgun;

use Interop\Container\ContainerInterface;
use Zend\Http\Client as HttpClient;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ClientFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, Client::class);
    }

    public function __invoke(ContainerInterface $container, $name, $options = [])
    {
        $config = $container->get('Config')['mailgun'];
        $httpClient = new HttpClient();
        $httpClient->setAuth('api', $config['key']);

        return new Client($httpClient, $config['domain']);
    }
}
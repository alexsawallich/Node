<?php
namespace Node\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Cache\StorageFactory;

/**
 * Gibt ein Cache-Objekt zurück.
 */
class NodeRouteCacheFactory implements FactoryInterface
{

    /**
     * (non-PHPdoc)
     * 
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return StorageFactory::factory([
            'adapter' => [
                'name' => 'filesystem',
                'options' => [
                    'ttl' => 3600,
                    'cache_dir' => './data/cache'
                ]
            ]
        ]);
    }
}

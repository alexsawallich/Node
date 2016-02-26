<?php
namespace Node\Model;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Stdlib\Hydrator\Strategy\SerializableStrategy;
use Zend\Serializer\Adapter\PhpSerialize;

class NodeHydratorFactory implements FactoryInterface
{
    /**
     * (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $hydrator = new ClassMethods();
        $hydrator->addStrategy('node_route_config', new SerializableStrategy(new PhpSerialize()));
        return $hydrator;
    }
}

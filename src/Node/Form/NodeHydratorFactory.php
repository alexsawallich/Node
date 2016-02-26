<?php
namespace Node\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Node\Form\Strategy\SerializeStrategy;

class NodeHydratorFactory implements FactoryInterface
{
    /**
     * (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $hydrator = new NodeHydrator();
        
        $strategy = new SerializeStrategy();
        $hydrator->addStrategy('node_route_config', $strategy);
        
        return $hydrator;
    }
}

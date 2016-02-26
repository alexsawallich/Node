<?php
namespace Node\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for instantiating the NodeBackendController.
 */
class BackendControllerFactory implements FactoryInterface
{
    /**
     * (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $controllerManager)
    {
        $nodeService = $controllerManager->getServiceLocator()->get('NodeService');
        return new BackendController($nodeService);
    }
}

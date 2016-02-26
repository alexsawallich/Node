<?php
namespace Node;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\EventManager\EventInterface;

/**
 * Module for maintaining all contents via the same interface called "nodes".
 */
class Module implements AutoloaderProviderInterface, BootstrapListenerInterface, ConfigProviderInterface
{

    /**
     * (non-PHPdoc)
     *
     * @see \Zend\ModuleManager\Feature\AutoloaderProviderInterface::getAutoloaderConfig()
     */
    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/../../src/' . str_replace('\\', '/', __NAMESPACE__)
                ]
            ]
        ];
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Zend\ModuleManager\Feature\ConfigProviderInterface::getConfig()
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Zend\ModuleManager\Feature\BootstrapListenerInterface::onBootstrap()
     */
    public function onBootstrap(EventInterface $event)
    {
        $eventManager = $event->getApplication()->getEventManager();
        
        // Register node-routes
        $serviceLocator = $event->getApplication()->getServiceManager();
        $nodeService = $serviceLocator->get('NodeService');
        $nodeService->injectRoutesIntoRouter();
        
        // Register Listeners
        $mvcListenerAggregate = $serviceLocator->get('NodeMvcListenerAggregate');
        $mvcListenerAggregate->attach($eventManager);
        
        $nodeServiceListenerAggregate = $serviceLocator->get('NodeServiceListenerAggregate');
        $nodeServiceListenerAggregate->attach($nodeService->getEventManager());
    }
}

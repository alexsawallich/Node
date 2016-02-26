<?php
namespace Node\ListenerAggregate;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;
use Zend\Mvc\Controller\Plugin\Redirect;

/**
 * Aggregate which is attached to the global MVC-EventManager.
 */
class MvcListenerAggregate extends AbstractListenerAggregate
{
    /**
     * (non-PHPdoc)
     * @see \Zend\EventManager\ListenerAggregateInterface::attach()
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, [$this, 'injectMatchedNode']);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER, [$this, 'injectIntoView'], -9000);
    }
    
    /**
     * Injects the node into the global MVC-Event-instance.
     * 
     * @param MvcEvent $event
     */
    public function injectMatchedNode(MvcEvent $event)
    {
        $routeMatch = $event->getRouteMatch(); /* @var $routeMatch \Zend\Mvc\Router\Http\RouteMatch */
        $name = $routeMatch->getMatchedRouteName();
        
        $serviceLocator = $event->getApplication()->getServiceManager();
        $node = $serviceLocator->get('NodeService')->getNode(str_replace('node-', '', $name));
        if (false != $node) {
            $event->setParam('matchedNode', $node);
            
            // Update access counter if enabled
            if (true == $serviceLocator->get('NodeOptions')->getEnableAccessCounter()) {
                $sessionContainer = new Container('node_access');
                $node->setNodeAccessDate(date('Y-m-d H:i:s'));
                if (false == $sessionContainer->{$node->getNodeId()}) {
                    $node->setNodeAccessCount($node->getNodeAccessCount()+1);
                    $sessionContainer->{$node->getNodeId()} = 1;
                }
                $serviceLocator->get('NodeService')->saveNode($node);
            }
            
            // Redirect if it's a Redirect-Node...
            if ('redirect' == $node->getNodeType()) {
                $response = $event->getResponse();
                $response->setStatusCode($node->getNodeRedirectCode());
                $response->getHeaders()->addHeaderLine('Location', $node->getNodeRedirectTarget());
                $response->sendHeaders();
            }
        } else {
            $event->setParam('matchedNode', null);
        }
    }
    
    /**
     * Sets some Meta-Data into the view.
     * 
     * @param MvcEvent $event
     */
    public function injectIntoView(MvcEvent $event)
    {
        $serviceLocator = $event->getApplication()->getServiceManager();
        $nodeOptions = $serviceLocator->get('NodeOptions'); /* @var $nodeOptions \Node\Options\NodeOptions */
        $viewHelperManager = $serviceLocator->get('viewhelpermanager');
        $serverurl = $viewHelperManager->get('serverurl');
        
        if (null != $event->getParam('matchedNode')) {
            $node = $event->getParam('matchedNode'); /* @var $node \Node\Entity\NodeInterface */
        
            // HeadTitle
            $headtitle = $viewHelperManager->get('headtitle');
            $headtitle->set($node->getNodeName());
        
            // Canonical
            $headlink = $viewHelperManager->get('headlink');
            $headlink([
                'rel' => 'canonical',
                'href' => $serverurl() . $node->getNodeRoute()
            ]);
            
            // Meta?
            if (true == $nodeOptions->getEnableMetaTags()) {
                $headmeta = $viewHelperManager->get('headmeta');
                if (null != $node->getNodeMetaRobots()) {
                    $headmeta->appendName('robots', $node->getNodeMetaRobots());
                }
                
                if (null != $node->getNodeMetaDescription()) {
                    $headmeta->appendName('description', $node->getNodeMetaDescription());
                }
                
                if (null != $node->getNodeMetaKeywords()) {
                    $headmeta->appendName('keywords', $node->getNodeMetaKeywords());
                }
            }
        
        }
    }
}

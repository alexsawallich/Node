<?php
namespace Node\Router;

use Zend\Mvc\Router\Http\TreeRouteStack;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Overrides the assemble of a route, so that the related node-route (if available) will be returned.
 */
class NodeRouter extends TreeRouteStack implements ServiceLocatorAwareInterface
{

    /**
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * In-object-cache for quried routes.
     *
     * @var array
     */
    protected $routeMap;

    /**
     * (non-PHPdoc)
     *
     * @see \Zend\Mvc\Router\Http\TreeRouteStack::assemble()
     */
    public function assemble(array $params = [], array $options = [])
    {
        // Generate orignal URL
        $url = parent::assemble($params, $options);
        
        // Fill Route-Map
        if (null === $this->routeMap) {
            $this->routeMap = [];
            $nodes = $this->getServiceLocator()
                ->get('NodeService')
                ->getNodes();
            foreach ($nodes as $node) {
                if (false == empty($node->getNodeOriginalRoute()) && 'redirect' != $node->getNodeType()) {
                    $this->routeMap[$node->getNodeOriginalRoute()] = $node->getNodeRoute();
                }
            }
        }
        
        // Get URL from Route-Map and return it
        if (true === isset($this->routeMap[$url])) {
            return $this->routeMap[$url];
        }
        
        // Return original URL
        return $url;
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Zend\ServiceManager\ServiceLocatorAwareInterface::getServiceLocator()
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Zend\ServiceManager\ServiceLocatorAwareInterface::setServiceLocator()
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }
}
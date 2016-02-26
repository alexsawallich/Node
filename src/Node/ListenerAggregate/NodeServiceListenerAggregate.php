<?php
namespace Node\ListenerAggregate;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventInterface;

class NodeServiceListenerAggregate extends AbstractListenerAggregate
{

    /**
     * (non-PHPdoc)
     * 
     * @see \Zend\EventManager\ListenerAggregateInterface::attach()
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('node.save.post', [$this, 'flushNodeCache']);
    }

    /**
     * Deletes the Cache generated from the nodes for the routes.
     * 
     * @param EventInterface $event
     */
    public function flushNodeCache(EventInterface $event)
    {
        $event->getTarget()
            ->getServiceLocator()
            ->get('NodeRouteCache')
            ->flush();
    }
}

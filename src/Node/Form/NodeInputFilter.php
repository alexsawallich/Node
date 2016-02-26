<?php
namespace Node\Form;

use Zend\InputFilter\InputFilter;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManager;

/**
 * Input-Filter for the Node-form.
 */
class NodeInputFilter extends InputFilter implements EventManagerAwareInterface
{
    /**
     * Will be used by the EventManager.
     * 
     * @var string
     */
    protected $eventIdentifier = 'nodeinputfilter';

    /**
     * EventManager-instance.
     * 
     * @var EventManagerInterface
     */
    protected $events;
    
    /**
     * NodeOptions-instance.
     * 
     * @var \Node\Options\NodeOptions
     */
    protected $nodeOptions;

    /**
     * (non-PHPdoc)
     *
     * @see \Zend\EventManager\EventsCapableInterface::getEventManager()
     */
    public function getEventManager()
    {
        if (null == $this->events) {
            $this->setEventManager(new EventManager());
        }
        
        return $this->events;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Zend\InputFilter\BaseInputFilter::init()
     */
    public function init()
    {
        $this->add([
            'name' => 'node_name',
            'required' => true
        ]);
        
        $this->add([
            'name' => 'node_route',
            'required' => true
        ]);
        
        $this->add([
            'type' => 'Zend\InputFilter\InputFilter',
            'controller' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'IsValidController'
                    ]
                ]
            ],
            'action' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'IsValidAction'
                    ]
                ]
            ],
            'params' => [
                'required' => false,
                'validators' => [
                    [
                        'name' => 'IsIniParsable'
                    ]
                ]
            ]
        ], 'node_route_config');
        
        if(true == $this->getNodeOptions()->getEnableMetaTags()) {
            $this->add([
                'type' => 'Zend\InputFilter\InputFilter',
                'node_meta_description' => [
                    'required' => false,
                    'validators' => [
                        [
                            'name' => 'StringLength',
                            'options' => [
                                'max' => 255
                            ]
                        ]
                    ]
                ],
                'node_meta_keywords' => [
                    'required' => false,
                    'validators' => [
                        [
                            'name' => 'StringLength',
                            'options' => [
                                'max' => 255
                            ]
                        ]
                    ]
                ],
                'node_meta_robots' => [
                    'required' => false,
                ],
            ], 'node_meta');
        }
        
        $this->getEventManager()->trigger('init', $this);
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Zend\EventManager\EventManagerAwareInterface::setEventManager()
     */
    public function setEventManager(EventManagerInterface $events)
    {
        $identifiers = $events->getIdentifiers();
        $identifiers += [$this->eventIdentifier, __CLASS__];
        $identifiers = array_unique($identifiers);
        $events->setIdentifiers($identifiers);
        $this->events = $events;
    }
    
    /**
     * Returns the NodeOptions.
     * 
     * @return \Node\Options\NodeOptions
     */
    public function getNodeOptions()
    {
        return $this->nodeOptions;
    }
    
    /**
     * Sets the NodeOptions into the filter.
     * 
     * @param \Node\Options\NodeOptions $nodeOptions
     */
    public function setNodeOptions(\Node\Options\NodeOptions $nodeOptions)
    {
        $this->nodeOptions = $nodeOptions;
    }
}

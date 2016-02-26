<?php
namespace Node\Form;

use Zend\Form\Form;
use Zend\Form\FormInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use Zend\Form\Fieldset;

/**
 * Form to create or edit nodes.
 */
class NodeForm extends Form implements EventManagerAwareInterface, ServiceLocatorAwareInterface
{

    /**
     * FormElementManager-instance.
     *
     * @var ServiceLocatorInterface
     */
    protected $formElementManager;

    /**
     * Will be used by the EventManager.
     *
     * @var string
     */
    protected $eventIdentifier = 'nodeform';

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
     * If the bound object is an content-node we remove some fields, that mustn't be modified.
     *
     * (non-PHPdoc)
     *
     * @see \Zend\Form\Form::bind()
     */
    public function bind($object, $flags = FormInterface::VALUES_NORMALIZED)
    {
        if ('mvc' != $object->getNodeType()) {
            $this->remove('node_route_config');
            $this->remove('node_original_route');
            $this->getInputFilter()->remove('node_route_config');
            $this->getInputFilter()->remove('node_original_route');
        }
        
        if ('redirect' == $object->getNodeType()) {
            if (true == $this->getNodeOptions()->getEnableMetaTags()) {
                $this->remove('node_meta');
                $this->getInputFilter()->remove('node_meta');
            }
        }
        
        if ('redirect' != $object->getNodeType()) {
            $this->remove('node_redirect_code');
            $this->remove('node_redirect_target');
            $this->getInputFilter()->remove('node_redirect_code');
            $this->getInputFilter()->remove('node_redirect_target');
        }
        
        parent::bind($object, $flags);
    }

    /**
     * Returns all registered Controllers within the application.
     *
     * @return array
     */
    public function getControllers()
    {
        $types = $this->getServiceLocator()
            ->get('controllermanager')
            ->getRegisteredServices();
        $keyArray = [
            'invokableClasses' => '',
            'factories' => '',
            'aliases' => ''
        ];
        $types = array_intersect_key($types, $keyArray);
        $return = [];
        foreach ($types as $type => $controllers) {
            foreach ($controllers as $controller) {
                $return[$controller] = $controller;
            }
        }
        
        return $return;
    }

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
     * @see \Zend\ServiceManager\ServiceLocatorAwareInterface::getServiceLocator()
     */
    public function getServiceLocator()
    {
        return $this->formElementManager->getServiceLocator();
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Zend\Form\Element::init()
     */
    public function init()
    {
        $this->add([
            'name' => 'node_type',
            'type' => 'select',
            'options' => [
                'label' => 'Node-Type',
                'description' => '',
                'value_options' => [
                    'mvc' => 'MVC-Node',
                    'redirect' => 'Redirect-Node',
                ]
            ]
        ]);
        
        $this->add([
            'name' => 'node_name',
            'type' => 'Text',
            'options' => [
                'label' => 'Name'
            ]
        ]);
        
        $this->add([
            'name' => 'node_original_route',
            'type' => 'Text',
            'options' => [
                'label' => 'Original URL',
                'description' => 'Wenn diese Kombination aus Controller und Action bereits von der Applikation über eine bestimmte URL erreichbar ist, muss diese hier eingetragen werden. Jegliches Vorkommen der URL wird dann ersetzt.'
            ]
        ]);
        
        $this->add([
            'name' => 'node_route',
            'type' => 'Text',
            'options' => [
                'label' => 'URL',
                'description' => 'URL-path where this node should be available. Only enter the path, without the domain!'
            ]
        ]);
        
        $this->add([
            'name' => 'node_redirect_target',
            'type' => 'Text',
            'options' => [
                'label' => 'Target',
                'description' => 'The Target-URL, where this node should redirect to.'
            ]
        ]);
        
        $this->add([
            'name' => 'node_redirect_code',
            'type' => 'Select',
            'options' => [
                'label' => 'HTTP-Status-Code',
                'description' => 'Select the HTTP-Status-Code which will be used for redirection.',
                'value_options' => [
                    '301' => '301',
                    '302' => '302',
                ]
            ]
        ]);
        
        $this->add([
            'name' => 'node_route_config',
            'type' => 'Fieldset',
            'options' => [
                'label' => 'Configuration'
            ]
        ]);
        
        $this->get('node_route_config')->add([
            'name' => 'controller',
            'type' => 'Select',
            'options' => [
                'label' => 'Controller',
                'empty_option' => '-- Select --',
                'value_options' => $this->getControllers()
            ]
        ]);
        
        $this->get('node_route_config')->add([
            'name' => 'action',
            'type' => 'Text',
            'options' => [
                'label' => 'Action'
            ]
        ]);
        
        $this->get('node_route_config')->add([
            'name' => 'params',
            'type' => 'Textarea',
            'options' => [
                'label' => 'Additional Parameters',
                'description' => 'These parameters have to be provided in INI-format. Please see the <a href="http://php.net/manual/de/function.parse-ini-file.php">PHP-Documentation</a> for an explanation.'
            ]
        ]);
        
        if (true == $this->getNodeOptions()->getEnableMetaTags()) {
            $this->add([
                'name' => 'node_meta',
                'type' => 'Fieldset',
                'options' => [
                    'label' => 'Meta'
                ]
            ]);
            
            $this->get('node_meta')->add([
                'name' => 'node_meta_description',
                'type' => 'Textarea',
                'options' => [
                    'label' => 'Meta-Description',
                    'description' => 'This description will be shown in relevant search-results in a Google-Search. If you use more than 156 chars (including spaces) your description will be truncated within the search results. Therefore it\'s recommended to use less than 156 chars.' 
                ]
            ]);
            
            $this->get('node_meta')->add([
                'name' => 'node_meta_keywords',
                'type' => 'Text',
                'options' => [
                    'label' => 'Meta-Keywords',
                    'description' => 'These keywords won\'t be considered by Google anymore, but some other search-engines may use them. Provide a comma-separated list of keywords.' 
                ]
            ]);
            
            $this->get('node_meta')->add([
                'name' => 'node_meta_robots',
                'type' => 'Select',
                'options' => [
                    'label' => 'Robots',
                    'description' => 'Chose a directive for search spiders.',
                    'empty_option' => '',
                    'value_options' => [
                        'index,follow' => 'index,follow',
                        'index,nofollow' => 'index,nofollow',
                        'noindex,follow' => 'noindex,follow',
                        'noindex,nofollow' => 'noindex,nofollow'
                    ]
                ]
            ]);
        }
        
        $this->add([
            'name' => 'submit',
            'type' => 'Submit',
            'options' => [
                'label' => 'Submit'
            ]
        ]);
        
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
        $identifiers += [
            $this->eventIdentifier,
            __CLASS__
        ];
        $identifiers = array_unique($identifiers);
        $events->setIdentifiers($identifiers);
        $this->events = $events;
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Zend\ServiceManager\ServiceLocatorAwareInterface::setServiceLocator()
     */
    public function setServiceLocator(ServiceLocatorInterface $formElementManager)
    {
        $this->formElementManager = $formElementManager;
    }
    
    /**
     * Returns the Node-Options.
     * 
     * @return \Node\Options\NodeOptions
     */
    public function getNodeOptions()
    {
        return $this->nodeOptions;
    }
    
    /**
     * Sets the Node-Options.
     * 
     * @param \Node\Options\NodeOptions $nodeOptions
     * @return \Node\Form\NodeForm
     */
    public function setNodeOptions(\Node\Options\NodeOptions $nodeOptions)
    {
        $this->nodeOptions = $nodeOptions;
        return $this;
    }
}

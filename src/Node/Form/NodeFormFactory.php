<?php
namespace Node\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for creating the Node-form.
 */
class NodeFormFactory implements FactoryInterface
{

    /**
     * (non-PHPdoc)
     * 
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $formElementManager)
    {
        $serviceLocator = $formElementManager->getServiceLocator();
        
        $form = new NodeForm();
        
        $hydrator = $serviceLocator->get('NodeFormHydrator');
        $form->setHydrator($hydrator);
        
        $form->setInputFilter($serviceLocator->get('inputfiltermanager')->get('NodeInputFilter'));
        
        $form->setNodeOptions($serviceLocator->get('NodeOptions'));
        
        return $form;
    }
}

<?php
namespace Node\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class NodeInputFilterFactory implements FactoryInterface
{
    /**
     * (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $inputFilterManager)
    {
        $filter = new NodeInputFilter();
        
        $nodeOptions = $inputFilterManager->getServiceLocator()->get('NodeOptions');
        $filter->setNodeOptions($nodeOptions);
        return $filter;
    }
}

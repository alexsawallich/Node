<?php
namespace Node\Model;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Node\Entity\ContentNode;
use Node\ResultSet\NodeSet;
use Node\Entity\MvcNode;
use Node\Entity\RedirectNode;

/**
 * Factory zum Erstellen des Node-Table-Gateways.
 */
class NodeTableFactory implements FactoryInterface
{

    /**
     * (non-PHPdoc)
     *
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $options = $serviceLocator->get('NodeOptions');
        
        $table = $options->getNodeTableName();
        $adapter = $serviceLocator->get($options->getNodeDbAdapterName('\Zend\Db\Adapter\Adapter'));
        
        $hydrator = $serviceLocator->get('NodeModelHydrator');
        $resultSet = new NodeSet($hydrator, ['content' => new ContentNode(), 'mvc' => new MvcNode(), 'redirect' => new RedirectNode()]);
        
        return new NodeTable($table, $adapter, null, $resultSet);
    }
}

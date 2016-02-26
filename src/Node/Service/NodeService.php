<?php
namespace Node\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\ResultSet\ResultSetInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManager;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Session\Container;
use Zend\Form\FormInterface;
use Zend\Db\Sql\Where;

/**
 * Service-Klasse, welche allgemeine Aufgaben des Moduls übernimmt.
 */
class NodeService implements EventManagerAwareInterface, ServiceLocatorAwareInterface
{

    /**
     * Instanz des Event-Managers.
     *
     * @var EventManagerInterface
     */
    protected $events;

    /**
     * Instanz des Node-Table-Gateways.
     *
     * @var \Node\Model\NodeTable
     */
    protected $nodeTable;

    /**
     * Instanz des ServiceLocators.
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    public function getDeletableNodes($ids)
    {
        $select = $this->getNodeTable()->getSelect();
        $select->where([
            'node_id' => $ids
        ]);
        $select->where->notIn('node_type', [
            'content'
        ]);
        return $this->getNodeTable()->selectWith($select);
    }

    /**
     * Löscht einen Node aus der Datenbank.
     *
     * @param \Node\Entity\NodeInterface $node            
     * @return Ambigous <number, \Zend\Db\TableGateway\mixed>
     */
    public function deleteNode(\Node\Entity\NodeInterface $node)
    {
        $this->getEventManager()->trigger('node.delete.pre', $this, compact('node'));
        $return = $this->getNodeTable()->delete([
            'node_id' => $node->getNodeId()
        ]);
        $this->getEventManager()->trigger('node.delete.post', $this, compact('node'));
        return $return;
    }
    
    public function getCurrentFilters()
    {
        $session = new Container('node');
        if (false === isset($session['filters'])) {
            $session['filters'] = [];
        }
        
        return $session['filters'];
    }

    /**
     * Gibt den Event-Manager zurück.
     *
     * @return \Zend\EventManager\EventManagerInterface
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
        return $this->serviceLocator;
    }

    /**
     * Gibt einen Node anhand dessen ID zurück.
     *
     * @param int $id            
     * @return Ambigous <false,\Node\Table\NodeInterface>
     */
    public function getNode($id)
    {
        return $this->getNodeTable()->getRowById($id);
    }

    /**
     * Gibt das Node-Formular zurück.
     *
     * @return \Node\Form\NodeForm
     */
    public function getNodeForm()
    {
        return $this->getServiceLocator()
            ->get('formelementmanager')
            ->get('NodeForm');
    }

    /**
     * Gibt alle Nodes zurück.
     *
     * @return \Node\Table\Ambigous
     */
    public function getNodes($ids = null)
    {
        if (null == $ids) {
            return $this->getNodeTable()->getRowset();
        } else {
            return $this->getNodeTable()->getRowsetByIds($ids);
        }
    }
    
    public function getFilterForm()
    {
        return $this->getServiceLocator()->get('formelementmanager')->get('Node\FilterForm');
    }
    
    public function getNodePaginator($currentPageNumber = 1, $filters = null)
    {
        // Select-Statement
        $select = $this->getNodeTable()->getSelect();
        
        /* @var $filters \Zend\Stdlib\ArrayObject */
        $filters = $filters->getArrayCopy();
        if (true === is_array($filters) && count($filters) > 0) {
            $where = new Where();
            if (true === isset($filters['type'])) {
                $where->and->equalTo('node_type', $filters['type']);
            }
            
            if (true === isset($filters['search'])) {
                $where->and->nest()->like('node_id', '%' . $filters['search'] . '%')
                             ->or->like('node_name', '%' . $filters['search'] . '%')
                             ->or->like('node_route', '%' . $filters['search'] . '%');
            }
            
            $select->where($where);
        }
        
        // ResultSet-Prototype
        $hydrator = $this->getServiceLocator()->get('NodeModelHydrator');
        $resultSet = $this->nodeTable->getResultSetPrototype();
        
        $adapter = new DbSelect($select, $this->getNodeTable()->getAdapter(), $resultSet);
        
        // Paginator
        $paginator = new Paginator($adapter);
        $paginator->setItemCountPerPage(25);
        $paginator->setCurrentPageNumber($currentPageNumber);
        
        return $paginator;
    }

    /**
     * Gibt das Node-Table-Gateway zurück.
     *
     * @return \Node\Model\NodeTable
     */
    public function getNodeTable()
    {
        if (null == $this->nodeTable) {
            $this->nodeTable = $this->getServiceLocator()->get('NodeTable');
        }
        
        return $this->nodeTable;
    }

    /**
     * Processes the filter form.
     * 
     * @param \Node\Form\FilterForm $form
     * @return boolean
     */
    public function processFilterForm(\Node\Form\FilterForm $form)
    {
        $request = $this->getServiceLocator()->get('request');
        if (true === $request->isPost()) {
            $form->setData($request->getPost());
            if (true === $form->isValid()) {
                $this->updateFiltersInSession($form->getData(FormInterface::VALUES_AS_ARRAY));
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Verarbeitet das Formular zum Erstellen eines Nodes.
     *
     * @param \Zend\Form\Form $form            
     * @return \Zend\Http\PhpEnvironment\Response|boolean
     */
    public function processNodeForm(\Zend\Form\Form $form)
    {
        $prg = $this->getServiceLocator()
            ->get('controllerpluginmanager')
            ->get('prg')
            ->__invoke();
        
        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            return $prg;
        } elseif (true === is_array($prg)) {
            $form->setData($prg);
            if (true == $form->isValid()) {
                $node = $form->getData();
                $this->saveNode($node);
                return true;
            }
        }
        
        return false;
    }

    /**
     * Verarbeitet das Formular zum Löschen von Nodes.
     *
     * @param ResultSetInterface $resultSet            
     * @return boolean
     */
    public function processNodeDeleteForm(ResultSetInterface $resultSet)
    {
        $request = $this->getServiceLocator()->get('request');
        if (false === $request->isPost()) {
            return false;
        }
        
        if (null === $request->getPost('delete')) {
            return false;
        }
        
        // Löschen
        foreach ($resultSet as $node) {
            $this->deleteNode($node);
        }
        
        return true;
    }

    /**
     * Speichert einen Node in der Datenbank.
     *
     * @param \Node\Entity\NodeInterface $node            
     */
    public function saveNode(\Node\Entity\NodeInterface $node)
    {
        $hydrator = $this->getServiceLocator()->get('NodeModelHydrator');
        $data = $hydrator->extract($node);
        
        $args = $this->getEventManager()->prepareArgs(compact('node', 'data'));
        $this->getEventManager()->trigger('node.save.pre', $this, $args);
        if (null == $node->getNodeId()) {
            $this->getEventManager()->trigger('node.insert.pre', $this, $args);
            $this->getNodeTable()->insert($data);
            $node->setNodeId($this->getNodeTable()
                ->getLastInsertValue());
            $this->getEventManager()->trigger('node.insert.post', $this, $args);
        } else {
            $this->getEventManager()->trigger('node.update.pre', $this, $args);
            $r = $this->nodeTable->update($data, [
                'node_id' => $node->getNodeId()
            ]);
            $this->getEventManager()->trigger('node.update.post', $this, $args);
        }
        $this->getEventManager()->trigger('node.save.post', $this, $args);
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Zend\EventManager\EventManagerAwareInterface::setEventManager()
     */
    public function setEventManager(EventManagerInterface $events)
    {
        $events->addIdentifiers([
            __CLASS__,
            'nodeservice'
        ]);
        
        $this->events = $events;
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
    
    protected function updateFiltersInSession($values)
    {
        $session = new Container('node');
        if (false === isset($session['filters'])) {
            $session['filters'] = [];
        }
        
        // Clear filters?
        /*if (true === isset($values['reset'])) {
            $session['filters'] = [];
            return;
        }*/
        
        // Update/set filter values
        $session['filters'] = array_intersect_key(array_filter($values), ['search' => '', 'type' => '']);
    }
    
    /**
     * Fetches all nodes from the database, generates a route-config and injects it into the router.
     */
    public function injectRoutesIntoRouter()
    {
        $router = $this->getServiceLocator()->get('router');
        $cache = $this->getServiceLocator()->get('NodeRouteCache');
        
        $cacheKey = 'node-routes-config';
        $routes = $cache->getItem($cacheKey, $success);
        if(false == $success) {
            $routes = [];
            $nodes = $this->getNodes();
            foreach ($nodes as $node) {
                $routeConfig = $node->getNodeRouteConfig();
                if (false == $routeConfig) {
                    $routeConfig = [];
                }
        
                $params = [];
        
                if (true === isset($routeConfig['params'])) {
                    $params = $routeConfig['params'];
                    unset($routeConfig['params']);
                }
        
                if (false === is_array($params)) {
                    $params = [$params];
                }
        
                $routeConfig = array_merge($routeConfig, $params);
                $routeConfig = array_filter($routeConfig);
        
                $routes['node-' . $node->getNodeId()] = [
                    'type' => 'Literal',
                    'options' => [
                        'route' => $node->getNodeRoute(),
                        'defaults' => $routeConfig
                    ]
                ];
            }
        
            $routes = serialize($routes);
            $cache->setItem($cacheKey, $routes);
        }
        
        $router->addRoutes(unserialize($routes));
    }
}

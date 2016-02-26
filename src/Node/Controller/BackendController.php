<?php
namespace Node\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\Filter\Word\CamelCaseToDash;
use Node\Entity\MvcNode;
use Zend\Stdlib\ArrayObject;

/**
 * Controller for managing Nodes.
 */
class BackendController extends AbstractActionController
{
    /**
     * (non-PHPdoc)
     * 
     * @see \Node\Service\NodeServiceAwareInterface::nodeService
     */
    protected $nodeService;
    
    /**
     * Stores the NodeService into the controller.
     * 
     * @param \Node\Service\NodeService $nodeService
     */
    public function __construct(\Node\Service\NodeService $nodeService)
    {
        $this->setNodeService($nodeService);
    }
    
    /**
     * Allows to create a new node
     */
    public function addAction()
    {
        // Form
        $form = $this->getNodeService()->getNodeForm();
        $nodeType = $this->params()->fromQuery('node_type', 'mvc');
        $nodeType = '\Node\Entity\\' . ucfirst($nodeType) . 'Node'; 
        
        $node = new $nodeType();
        $form->bind($node);
        
        // Processing
        $process = $this->getNodeService()->processNodeForm($form);
        if ($process instanceof \Zend\Http\PhpEnvironment\Response) {
            return $process;
        } elseif(true === $process) {
            $this->flashmessenger()->addSuccessMessage(sprintf($this->translate('The node %s has been saved.'), $node->getNodeName()));
            return $this->redirect()->toRoute('zfcadmin/node');
        }
        
        // View
        return [
            'form' => $form,
            'node' => $node,
        ];
    }
    
    /**
     * Entrypoint for batch-processing.
     */
    public function batchAction()
    {
        // Get action to forward to from POST
        $action = $this->params()->fromPost('action');
    
        // Check if it's in whitelist
        if (false === in_array($action, ['delete'])) {
            return $this->notFoundAction();
        }
    
        // Forwarding
        return $this->forward()->dispatch('Node\Controller\Backend', ['action' => $action]);
    }
    
    /**
     * Allows to delete one or more nodes.
     * 
     * Content-Nodes cannot be deleted with this action.
     */
    public function deleteAction()
    {
        // ID holen
        $getId = [$this->params()->fromRoute('id')];
        $postId = $this->params()->fromPost('id', []);
        $ids = array_merge($getId, $postId);
        array_unique($ids);
        $ids = array_filter($ids);
        
        // Prüfen, ob noch IDs vorhanden sind
        if (0 == count($ids)) {
            $this->flashmessenger()->addErrorMessage($this->translate('No nodes have been selected.'));
            return $this->redirect()->toRoute('zfcadmin/node');
        }
        
        // Datensätze holen
        $nodes = $this->getNodeService()->getDeletableNodes($ids);
        if(0 == count($nodes)) {
            $this->flashmessenger()->addErrorMessage($this->translate('Only invalid nodes have been selected.'));
            return $this->redirect()->toRoute('zfcadmin/node');
        }
        
        // Formularverarbeitung
        if (true == $this->getNodeService()->processNodeDeleteForm($nodes->buffer())) {
            $message = $this->translate('The node has been successfully deleted.');
            if (count($nodes) > 1)
                $message = $this->translate('The nodes have been successfully deleted.');
            $this->flashmessenger()->addSuccessMessage($message);
            return $this->redirect()->toRoute('zfcadmin/node');
        }
        
        // View
        return [
            'nodes' => $nodes
        ];
    }
    
    /**
     * Allows to edit a node.
     */
    public function editAction()
    {
        // Node holen
        $id = $this->params()->fromRoute('id');
        $node = $this->getNodeService()->getNode($id);
        if (false == $node) {
            $this->flashmessenger()->addErrorMessage($this->translate('Invalid node selected.'));
            return $this->redirect()->toRoute('zfcadmin/node');
        }
        
        // Formular
        $form = $this->getNodeService()->getNodeForm();
        $form->remove('node_type')
             ->getInputFilter()->remove('node_type');
        $form->bind($node);
        
        // Formularverarbeitung
        $process = $this->getNodeService()->processNodeForm($form);
        if ($process instanceof \Zend\Http\PhpEnvironment\Response) {
            return $process;
        } elseif(true === $process) {
            $this->flashmessenger()->addSuccessMessage(sprintf($this->translate('The node %s has been successfully saved.'), $node->getNodeName()));
            return $this->redirect()->toRoute('zfcadmin/node');
        } 
        
        // View
        return [
            'form' => $form,
            'node' => $node
        ];
    }

    /**
     * Shows an paginated overview of all nodes.
     */
    public function indexAction()
    {
        // Filter form
        $form = $this->getNodeService()->getFilterForm();
        $filters = new ArrayObject($this->getNodeService()->getCurrentFilters());
        $form->bind($filters);
        
        // Process filter form if necessary
        $process = $this->getNodeService()->processFilterForm($form);
        if (true === $process) {
            return $this->redirect()->toRoute('zfcadmin/node');
        }
        
        // Determinde current page number
        $currentPageNumber = $this->params()->fromQuery('page', 1);
        
        // Get paginated nodes
        $nodes = $this->getNodeService()->getNodePaginator($currentPageNumber, $filters);
        
        // Show stats?
        $enableAccessCounter = $this->getServiceLocator()->get('NodeOptions')->getEnableAccessCounter();
        
        // View
        return [
            'nodes' => $nodes,
            'form' => $form,
            'isFiltered' => true,
            'enableAccessCounter' => $enableAccessCounter,
        ];
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Node\Service\NodeServiceAwareInterface::getNodeService()
     */
    public function getNodeService()
    {
        return $this->nodeService;
    }
    
    /**
     * Loads available actions of a controller.
     */
    public function loadActionsAction()
    {
        if (false == $this->getRequest()->isPost()) {
            return false;
        }
        
        // Get controller
        $controller = $this->params()->fromPost('controller');
        
        // Fetch actions aof this controller
        $controllerInstance = $this->getServiceLocator()->get('controllermanager')->get($controller);
        $methods = get_class_methods($controllerInstance);
        $actions = array_filter($methods, function($item) {
            if (preg_match('#Action$#', $item) && 'notFoundAction' != $item && 'getMethodFromAction' != $item) {
                return true;
            }
            
            return false;
        });
        
        // Sort
        sort($actions);
        
        $filter = new CamelCaseToDash();
        $actions = array_map(function($method) use ($filter) {
            $action = preg_replace('#^(.*?)Action$#u', '$1', $method);
            $action = $filter->filter($action);
            return $action;
        }, $actions);
        
        // View
        return new JsonModel(compact([
            'actions'
        ]));
    }
    
    /**
     * Translates a string.
     * 
     * @param string $string
     * @return string
     */
    protected function translate($string)
    {
        return $this->getServiceLocator()->get('translator')->translate($string);
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Node\Service\NodeServiceAwareInterface::setNodeService()
     */
    public function setNodeService(\Node\Service\NodeService $nodeService)
    {
        $this->nodeService = $nodeService;
    }
}

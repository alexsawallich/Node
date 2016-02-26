<?php
namespace Node\Entity;

/**
 * Introduces basic functionality of a node.
 */
abstract class AbstractNode implements NodeInterface
{

    /**
     * ID of the node.
     *
     * @var int
     */
    protected $node_id;
    
    /**
     * Counter the number of accesses for this node.
     * 
     * @var int
     */
    protected $node_access_count;
    
    /**
     * Date, when the node was accessed last.
     * 
     * @var string
     */
    protected $node_access_date;

    /**
     * Name of the node.
     *
     * @var string
     */
    protected $node_name;

    /**
     * Original URL, if available.
     *
     * @var string
     */
    protected $node_original_route;
    
    /**
     * URL of the node.
     *
     * @var string
     */
    protected $node_route;

    /**
     * Configuration of the routing-parameters of the node.
     *
     * @var string|array
     */
    protected $node_route_config;

    /**
     * (non-PHPdoc)
     *
     * @see \Node\Entity\NodeInterface::getNodeId()
     */
    public function getNodeId()
    {
        return $this->node_id;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Node\Entity\NodeInterface::getNodeAccessCount()
     */
    public function getNodeAccessCount()
    {
        return $this->node_access_count;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Node\Entity\NodeInterface::getNodeAccessDate()
     */
    public function getNodeAccessDate()
    {
        return $this->node_access_date;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Node\Entity\NodeInterface::getNodeName()
     */
    public function getNodeName()
    {
        return $this->node_name;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Node\Entity\NodeInterface::getNodeOriginalRoute()
     */
    public function getNodeOriginalRoute()
    {
        return $this->node_original_route;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Node\Entity\NodeInterface::getNodeRoute()
     */
    public function getNodeRoute()
    {
        return $this->node_route;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Node\Entity\NodeInterface::getNodeRouteConfig()
     */
    public function getNodeRouteConfig()
    {
        return $this->node_route_config;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Node\Entity\NodeInterface::getNodeType()
     */
    public abstract function getNodeType();

    /**
     * (non-PHPdoc)
     *
     * @see \Node\Entity\NodeInterface::setNodeId()
     */
    public function setNodeId($id)
    {
        $this->node_id = $id;
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Node\Entity\NodeInterface::setNodeAccessCount()
     */
    public function setNodeAccessCount($count)
    {
        $this->node_access_count = $count;
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Node\Entity\NodeInterface::setNodeAccessDate()
     */
    public function setNodeAccessDate($date)
    {
        $this->node_access_date = $date;
        return $this;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Node\Entity\NodeInterface::setNodeName()
     */
    public function setNodeName($name)
    {
        $this->node_name = $name;
        return $this;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Node\Entity\NodeInterface::setNodeOriginalRoute()
     */
    public function setNodeOriginalRoute($route)
    {
        $this->node_original_route = $route;
        return $this;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Node\Entity\NodeInterface::setNodeRoute()
     */
    public function setNodeRoute($route)
    {
        $this->node_route = $route;
        return $this;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Node\Entity\NodeInterface::setNodeRouteConfig()
     */
    public function setNodeRouteConfig($config)
    {
        $this->node_route_config = $config;
        return $this;
    }
}

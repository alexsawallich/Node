<?php
namespace Node\Entity;

/**
 * Interface that a node of any kind must implement.
 */
interface NodeInterface
{

    /**
     * Returns the id of the node.
     * 
     * @return int
     */
    public function getNodeId();
    
    public function getNodeAccessCount();
    
    public function getNodeAccessDate();
    
    /**
     * Returns the name of the node.
     * 
     * @return string
     */
    public function getNodeName();

    /**
     * Returns the original URL of the node, if available.
     * 
     * @return string
     */
    public function getNodeOriginalRoute();

    /**
     * Returns the URL of the node.
     * 
     * @return string
     */
    public function getNodeRoute();

    /**
     * Returns the route-config of the node.
     * 
     * @return string|array
     */
    public function getNodeRouteConfig();

    /**
     * Returns the type of this node.
     * 
     * @return string
     */
    public function getNodeType();

    /**
     * Sets the id of the node.
     * 
     * @param int $id
     */
    public function setNodeId($id);
    
    public function setNodeAccessCount($count);
    
    public function setNodeAccessDate($date);
    
    /**
     * Sets the name of the node.
     * 
     * @param string $name
     */
    public function setNodeName($name);

    /**
     * Sets the original URL of the node.
     * 
     * @param string $route
     */
    public function setNodeOriginalRoute($route);
    
    /**
     * Sets the URL of the node.
     * 
     * @param string $route
     */
    public function setNodeRoute($route);

    /**
     * Sets the route-configuration of the nodde.
     * 
     * @param string|array $config
     */
    public function setNodeRouteConfig($config);
}

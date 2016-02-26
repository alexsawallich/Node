<?php
namespace Node\Entity;

/**
 * Represents an MVC-Node.
 */
class MvcNode extends AbstractNode
{
    /**
     * Meta-Description of the node.
     *
     * @var string
     */
    protected $node_meta_description;
    
    /**
     * Meta-Keywords of the node.
     *
     * @var string
     */
    protected $node_meta_keywords;
    
    /**
     * Robots-Meta-Tag of the node.
     *
     * @var string
     */
    protected $node_meta_robots;

    /**
     * Returns the Meta-Description of the node.
     * 
     * @return string
     */
    public function getNodeMetaDescription()
    {
        return $this->node_meta_description;
    }
    
    /**
     * Returns the Meta-Keywords of the node.
     * 
     * @return string
     */
    public function getNodeMetaKeywords()
    {
        return $this->node_meta_keywords;
    }
    
    /**
     * Returns the Robots-informatione of the node.
     * 
     * @return string
     */
    public function getNodeMetaRobots()
    {
        return $this->node_meta_robots;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Node\Entity\AbstractNode::getNodeType()
     */
    public function getNodeType()
    {
        return 'mvc';
    }

    /**
     * Sets the Meta-Description of the node.
     * 
     * @param string $description
     * @return \Node\Entity\MvcNode
     */
    public function setNodeMetaDescription($description)
    {
        $this->node_meta_description = $description;
        return $this;
    }
    
    /**
     * Sets the Meta-Keywords of the node.
     * 
     * @param string $keywords
     * @return \Node\Entity\MvcNode
     */
    public function setNodeMetaKeywords($keywords)
    {
        $this->node_meta_keywords = $keywords;
        return $this;
    }
    
    /**
     * Sets the Robots-information of the node.
     * 
     * @param string $robots
     * @return \Node\Entity\MvcNode
     */
    public function setNodeMetaRobots($robots)
    {
        $this->node_meta_robots = $robots;
        return $this;
    }
}
<?php
namespace Node\Entity;

/**
 * Represents a content-node.
 */
class ContentNode extends MvcNode
{

    /**
     * This string identifies the related content.
     * 
     * @var string
     */
    protected $node_content_id;
    
    /**
     * Returns the identifying string of the related content.
     * 
     * @return string
     */
    public function getNodeContentId()
    {
        return $this->node_content_id;
    }
    
    /**
     * (non-PHPdoc)
     * 
     * @see \Node\Entity\AbstractNode::getNodeType()
     */
    public function getNodeType()
    {
        return 'content';
    }
    
    /**
     * Sets the identifying string of the related content.
     * 
     * @param string $id
     * @return \Node\Entity\ContentNode
     */
    public function setNodeContentId($id)
    {
        $this->node_content_id = $id;
        return $this;
    }
}
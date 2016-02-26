<?php
namespace Node\Entity;

trait NodeAwareTrait
{
    /**
     * The related node for this object.
     * 
     * @var \Node\Entity\NodeInterface
     */
    protected $node;
    
    /**
     * Returns the node from the object.
     * 
     * @return \Node\Entity\NodeInterface
     */
    public function getNode()
    {
        return $this->node;
    }
    
    /**
     * Sets the node within the object.
     * 
     * @param \Node\Entity\NodeInterface $node
     */
    public function setNode(\Node\Entity\NodeInterface $node)
    {
        $this->node = $node;
    }
}

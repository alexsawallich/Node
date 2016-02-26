<?php
namespace Node\Entity;

use Node\Entity\AbstractNode;

/**
 * Reprents a RedirctNode, which redirects a request to a complete new URL.
 */
class RedirectNode extends AbstractNode
{
    /**
     * The http-status-code the node will use upon redirection.
     * 
     * @var int
     */
    protected $node_redirect_code;
    
    /**
     * The URL the node will redirect to.
     * 
     * @var string
     */
    protected $node_redirect_target;
    
    /**
     * Returns the http-status-code the redirection will be used with.
     * 
     * @return int
     */
    public function getNodeRedirectCode()
    {
        return $this->node_redirect_code;
    }
    
    /**
     * Returns the URL the node will redirect to.
     * 
     * @return string
     */
    public function getNodeRedirectTarget()
    {
        return $this->node_redirect_target;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Node\Entity\AbstractNode::getNodeType()
     */
    public function getNodeType()
    {
        return 'redirect';
    }
    
    /**
     * Sets the http-status-code the node will use upon redirection.
     * 
     * @param int $code
     * @return \Node\Entity\RedirectNode
     */
    public function setNodeRedirectCode($code)
    {
        $this->node_redirect_code = $code;
        return $this;
    }
    
    /**
     * Sets the URL the node will redirect to.
     * 
     * @param string $target
     * @return \Node\Entity\RedirectNode
     */
    public function setNodeRedirectTarget($target)
    {
        $this->node_redirect_target = $target;
        return $this;
    }
}

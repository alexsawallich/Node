<?php
namespace Node\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Options-Object for the Node-Module.
 */
class NodeOptions extends AbstractOptions
{
    protected $enable_access_counter;
    
    protected $enable_meta_tags;
    
    public function getEnableAccessCounter()
    {
        return $this->enable_access_counter;
    }
    
    public function setEnableAccessCounter($enable_access_counter)
    {
        $this->enable_access_counter = $enable_access_counter;
    }
    
    /**
     * 
     * @return unknown
     */
    public function getEnableMetaTags()
    {
        return $this->enable_meta_tags;
    }
    
    /**
     * 
     * @param unknown $enable_meta_tags
     */
    public function setEnableMetaTags($enable_meta_tags)
    {
        $this->enable_meta_tags = $enable_meta_tags;
    }
}

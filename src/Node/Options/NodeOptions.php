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
    
    protected $node_db_adapter_name;
    
    protected $node_table_name;
    
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
     * @return boolean
     */
    public function getEnableMetaTags()
    {
        return $this->enable_meta_tags;
    }
    
    /**
     * 
     * @param boolean $enable_meta_tags
     */
    public function setEnableMetaTags($enable_meta_tags)
    {
        $this->enable_meta_tags = $enable_meta_tags;
    }
    
    public function getNodeTableName()
    {
        return $this->node_table_name;
    }
    
    public function setNodeTableName($name)
    {
        $this->node_table_name = $name;
    }
    
    public function getNodeDbAdapterName()
    {
        return $this->node_db_adapter_name;
    }
    
    public function setNodeDbAdapterName($name)
    {
        $this->node_db_adapter_name = $name;
    }
}

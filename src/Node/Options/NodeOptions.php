<?php
namespace Node\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Options-Object for the Node-Module.
 */
class NodeOptions extends AbstractOptions
{
    /**
     * Decides if node-accesses should be counted.
     * 
     * @var boolean
     */
    protected $enable_access_counter;
    
    /**
     * Decides if forms should be extended with meta-fields.
     * 
     * @var boolean
     */
    protected $enable_meta_tags;
    
    /**
     * Key of the db-adapter registered in the service-manager.
     * 
     * @var string
     */
    protected $node_db_adapter_name;
    
    /**
     * Name of the database-table.
     * 
     * @var string
     */
    protected $node_table_name;
    
    /**
     * Returns if node-accesses should be counted.
     * 
     * @return boolean
     */
    public function getEnableAccessCounter()
    {
        return $this->enable_access_counter;
    }
    
    /**
     * Sets if node-accesses should be counted.
     * 
     * @param unknown $enable_access_counter
     */
    public function setEnableAccessCounter($enable_access_counter)
    {
        $this->enable_access_counter = $enable_access_counter;
    }
    
    /**
     * Return if forms should be extended with meta-fields.
     * 
     * @return boolean
     */
    public function getEnableMetaTags()
    {
        return $this->enable_meta_tags;
    }
    
    /**
     * Sets if forms should be extended with meta fields.
     * 
     * @param boolean $enable_meta_tags
     */
    public function setEnableMetaTags($enable_meta_tags)
    {
        $this->enable_meta_tags = $enable_meta_tags;
    }
    
    /**
     * Returns name of the database-table.
     * 
     * @return string
     */
    public function getNodeTableName()
    {
        return $this->node_table_name;
    }
    
    /**
     * Sets name of the database-table.
     * 
     * @param string $name
     */
    public function setNodeTableName($name)
    {
        $this->node_table_name = $name;
    }
    
    /**
     * Returns the key which the db-adapter was registered with in the service-manager.
     * 
     * @return string
     */
    public function getNodeDbAdapterName()
    {
        return $this->node_db_adapter_name;
    }
    
    /**
     * Sets the key which the db-adapter was registered with in the service-manager.
     * 
     * @param string $name
     */
    public function setNodeDbAdapterName($name)
    {
        $this->node_db_adapter_name = $name;
    }
}

<?php
namespace Node\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

/**
 * Table-Gateway für die Node-Tabelle.
 */
class NodeTable extends TableGateway
{

    /**
     * Gibt einen Datensatz anhand dessen ID zurück.
     *
     * @param int $id            
     * @return Ambigous <false, \Node\Entity\NodeInterface>
     */
    public function getRowById($id)
    {
        $select = $this->getSelect()->where([
            'node_id' => (int) $id
        ]);
        return $this->selectWith($select)->current();
    }

    /**
     * Gibt alle Datensätze zurück.
     *
     * @return Ambigous <NULL, \Zend\Db\ResultSet\ResultSetInterface>
     */
    public function getRowset()
    {
        return $this->selectWith($this->getSelect());
    }

    /**
     * Gibt mehrere Nodes anhand deren IDs zudück.
     * 
     * @param array $ids            
     * @return Ambigous <NULL, \Zend\Db\ResultSet\ResultSetInterface>
     */
    public function getRowsetByIds($ids)
    {
        $select = $this->getSelect()->where([
            'node_id' => $ids
        ]);
        return $this->selectWith($select);
    }

    /**
     * Gibt ein allgemeines Select-Statement zurück.
     *
     * @return \Zend\Db\Sql\Select
     */
    public function getSelect()
    {
        $select = new Select($this->table);
        return $select;
    }
}

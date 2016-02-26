<?php
namespace Node\ResultSet;

use Zend\Db\ResultSet\Exception\InvalidArgumentException;
use Zend\Db\ResultSet\AbstractResultSet;
use Zend\Stdlib\Hydrator\HydratorInterface;

class NodeSet extends AbstractResultSet
{

    /**
     * @var HydratorInterface
     */
    protected $hydrator = null;
    
    protected $objectPrototypes = [
        'content' => null,
        'mvc' => null,
        'redirect' => null,
    ];

    public function __construct($hydrator = null, $objectPrototypes = [])
    {
        $this->setHydrator($hydrator);
        $this->setObjectPrototypes($objectPrototypes);
    }
    
    public function current()
    {
        if ($this->buffer === null) {
            $this->buffer = -2; // implicitly disable buffering from here on
        } elseif (is_array($this->buffer) && isset($this->buffer[$this->position])) {
            return $this->buffer[$this->position];
        }
        $data = $this->dataSource->current();
        $object = is_array($data) ? $this->hydrator->hydrate($data, clone $this->objectPrototypes[$data['node_type']]) : false;
    
        if (is_array($this->buffer)) {
            $this->buffer[$this->position] = $object;
        }
    
        return $object;
    }

    public function getObjectPrototype($type)
    {
        return $this->objectPrototype[$type];
    }

    public function setObjectPrototype($type, $objectPrototype)
    {
        if (! is_object($objectPrototype)) {
            throw new InvalidArgumentException('An object must be set as the object prototype, a ' . gettype($objectPrototype) . ' was provided.');
        }
        $this->objectPrototypes[$type] = $objectPrototype;
        return $this;
    }

    public function setObjectPrototypes(array $prototypes)
    {
        if (true === isset($prototypes['mvc'])) {
            $this->setObjectPrototype('mvc', $prototypes['mvc']);
        }
        
        if (true === isset($prototypes['content'])) {
            $this->setObjectPrototype('content', $prototypes['content']);
        }
        
        if (true === isset($prototypes['redirect'])) {
            $this->setObjectPrototype('redirect', $prototypes['redirect']);
        }
    }
    
    /**
     * Set the hydrator to use for each row object
     *
     * @param HydratorInterface $hydrator
     * @return HydratingResultSet
     */
    public function setHydrator(HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;
        return $this;
    }
    
    /**
     * Get the hydrator to use for each row object
     *
     * @return HydratorInterface
     */
    public function getHydrator()
    {
        return $this->hydrator;
    }

    public function toArray()
    {
        $return = array();
        foreach ($this as $row) {
            $return[] = $this->getHydrator()->extract($row);
        }
        return $return;
    }
}

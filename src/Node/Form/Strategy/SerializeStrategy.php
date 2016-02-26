<?php
namespace Node\Form\Strategy;

use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;
use Zend\Config\Reader\Ini as IniReader;
use Zend\Config\Writer\Ini as IniWriter;

class SerializeStrategy implements StrategyInterface
{

    /**
     * (non-PHPdoc)
     * 
     * @see \Zend\Stdlib\Hydrator\Strategy\StrategyInterface::extract()
     */
    public function extract($value)
    {
        if (true === isset($value['params']) && true === is_array($value['params'])) {
            $config = new IniWriter();
            $config->setRenderWithoutSectionsFlags(true);
            $value['params'] = $config->toString($value['params']);
        } else {
            $value['params'] = '';
        }
        
        return $value;
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Zend\Stdlib\Hydrator\Strategy\StrategyInterface::hydrate()
     */
    public function hydrate($value)
    {
        if ('' != $value['params']) {
            $config = new IniReader();
            $value['params'] = $config->fromString($value['params']);
        }
        
        return $value;
    }
}

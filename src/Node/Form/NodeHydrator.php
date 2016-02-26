<?php
namespace Node\Form;

use Zend\Stdlib\Hydrator\ClassMethods;
use Traversable;
use Zend\Stdlib\Exception;
use Zend\Stdlib\Hydrator\Filter\FilterComposite;
use Zend\Stdlib\Hydrator\Filter\FilterProviderInterface;
use Zend\Stdlib\Hydrator\Filter\GetFilter;
use Zend\Stdlib\Hydrator\Filter\HasFilter;
use Zend\Stdlib\Hydrator\Filter\IsFilter;
use Zend\Stdlib\Hydrator\Filter\MethodMatchFilter;
use Zend\Stdlib\Hydrator\Filter\OptionalParametersFilter;

class NodeHydrator extends ClassMethods
{
    protected $fieldsetMap = [
        'node_meta_description' => 'node_meta',
        'node_meta_keywords' => 'node_meta',
        'node_meta_robots' => 'node_meta',
    ];
    
    private $hydrationMethodsCache = array();
    
    /**
     * A map of extraction methods to property name to be used during extraction, indexed
     * by class name and method name
     *
     * @var string[][]
    */
    private $extractionMethodsCache = array();
    
    /**
     * Flag defining whether array keys are underscore-separated (true) or camel case (false)
     *
     * @var bool
    */
    protected $underscoreSeparatedKeys = true;
    
    /**
     * @var \Zend\Stdlib\Hydrator\Filter\FilterInterface
     */
    private $callableMethodFilter;
    
    public function __construct($underscoreSeparatedKeys = true)
    {
        parent::__construct();
        $this->setUnderscoreSeparatedKeys($underscoreSeparatedKeys);
    
        $this->callableMethodFilter = new OptionalParametersFilter();
    
        $this->filterComposite->addFilter('is', new IsFilter());
        $this->filterComposite->addFilter('has', new HasFilter());
        $this->filterComposite->addFilter('get', new GetFilter());
        $this->filterComposite->addFilter('parameter', new OptionalParametersFilter(), FilterComposite::CONDITION_AND);
    }
    
    /**
     * (non-PHPdoc)
     * @see \Zend\Stdlib\Hydrator\ClassMethods::extract()
     */
    public function extract($object)
    {
        if (!is_object($object)) {
            throw new Exception\BadMethodCallException(sprintf(
                '%s expects the provided $object to be a PHP object)',
                __METHOD__
            ));
        }
    
        $objectClass = get_class($object);
    
        // reset the hydrator's hydrator's cache for this object, as the filter may be per-instance
        if ($object instanceof FilterProviderInterface) {
            $this->extractionMethodsCache[$objectClass] = null;
        }
    
        // pass 1 - finding out which properties can be extracted, with which methods (populate hydration cache)
        if (! isset($this->extractionMethodsCache[$objectClass])) {
            $this->extractionMethodsCache[$objectClass] = array();
            $filter                                     = $this->filterComposite;
            $methods                                    = get_class_methods($object);
    
            if ($object instanceof FilterProviderInterface) {
                $filter = new FilterComposite(
                    array($object->getFilter()),
                    array(new MethodMatchFilter('getFilter'))
                );
            }
    
            foreach ($methods as $method) {
                $methodFqn = $objectClass . '::' . $method;
    
                if (! ($filter->filter($methodFqn) && $this->callableMethodFilter->filter($methodFqn))) {
                    continue;
                }
    
                $attribute = $method;
    
                if (strpos($method, 'get') === 0) {
                    $attribute = substr($method, 3);
                    if (!property_exists($object, $attribute)) {
                        $attribute = lcfirst($attribute);
                    }
                }
    
                $this->extractionMethodsCache[$objectClass][$method] = $attribute;
            }
        }
        
        #echo '<pre>';print_r($this->extractionMethodsCache[$objectClass]);echo '</pre>';exit;
    
        $values = array();
    
        // pass 2 - actually extract data
        foreach ($this->extractionMethodsCache[$objectClass] as $methodName => $attributeName) {
            $realAttributeName          = $this->extractName($attributeName, $object);
            
            if (true === isset($this->fieldsetMap[$realAttributeName])) {
                $values[$this->fieldsetMap[$realAttributeName]][$realAttributeName] = $this->extractValue($realAttributeName, $object->$methodName(), $object);
            } else {
                $values[$realAttributeName] = $this->extractValue($realAttributeName, $object->$methodName(), $object);
            }
        }
    
        return $values;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Zend\Stdlib\Hydrator\ClassMethods::hydrate()
     */
    public function hydrate(array $data, $object)
    {
        if (!is_object($object)) {
            throw new Exception\BadMethodCallException(sprintf(
                '%s expects the provided $object to be a PHP object)',
                __METHOD__
            ));
        }
    
        $objectClass = get_class($object);
    
        foreach ($data as $property => $value) {
            if (true === in_array($property, $this->fieldsetMap)) {
                $this->hydrate($value, $object);
                continue;
            }
            
            $propertyFqn = $objectClass . '::$' . $property;
    
            if (! isset($this->hydrationMethodsCache[$propertyFqn])) {
                $setterName = 'set' . ucfirst($this->hydrateName($property, $data));
    
                $this->hydrationMethodsCache[$propertyFqn] = is_callable(array($object, $setterName))
                ? $setterName
                : false;
            }
    
            if ($this->hydrationMethodsCache[$propertyFqn]) {
                $object->{$this->hydrationMethodsCache[$propertyFqn]}($this->hydrateValue($property, $value, $data));
            }
        }
    
        return $object;
    }
}

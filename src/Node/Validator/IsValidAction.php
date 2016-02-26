<?php
namespace Node\Validator;

use Zend\Validator\AbstractValidator;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Filter\Word\DashToCamelCase;

/**
 * Validator, der prüft, ob eine bestimmte Action in einem Controller ist.
 */
class IsValidAction extends AbstractValidator implements ServiceLocatorAwareInterface
{

    const INVALID_ACTION = 'invalid_action';
    const INVALID_CONTROLLER = 'invalid_controller';

    /**
     * Fehlermeldungen.
     *
     * @var array
     */
    protected $messageTemplates = [
        self::INVALID_ACTION => 'Die Action %value% existiert im gewählten Controller nicht.',
        self::INVALID_CONTROLLER => 'Bitte wählen Sie zunächst einen gültigen Controller.'
    ];

    /**
     * Instanz des Service-Locators.
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Prüft, ob der Controller initialisiert werden kann.
     *
     * @param string $controller            
     * @return boolean
     */
    protected function controllerExists($controller)
    {
        try {
            $this->getServiceLocator()
                ->get('controllermanager')
                ->get((string) $controller);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Zend\ServiceManager\ServiceLocatorAwareInterface::getServiceLocator()
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Zend\Validator\ValidatorInterface::isValid()
     */
    public function isValid($value, $context = [])
    {
        // Controller prüfen
        if (false === isset($context['controller'])) {
            $this->error(self::INVALID_CONTROLLER);
            return false;
        }
        
        $controller = $context['controller'];
        if (false == $this->controllerExists($controller)) {
            $this->error(self::INVALID_CONTROLLER);
            return false;
        }
        
        // Action prüfen
        $controller = $this->getServiceLocator()->get('controllermanager')->get($controller);
        $filter = new DashToCamelCase();
        $action = $filter->filter($value) . 'Action';
        if (false === method_exists($controller, $action)) {
            $this->error(self::INVALID_ACTION);
            return false;
        }
        
        return true;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Zend\ServiceManager\ServiceLocatorAwareInterface::setServiceLocator()
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator->getServiceLocator();
        return $this;
    }
}

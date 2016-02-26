<?php
namespace Node\Validator;

use Zend\Validator\AbstractValidator;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Validator, der prüft, ob ein Controller in der Anwendung registriert ist.
 */
class IsValidController extends AbstractValidator implements ServiceLocatorAwareInterface
{

    const INVALID_CONTROLLER = 'invalid_controller';

    /**
     * Fehlermeldungen.
     *
     * @var array
     */
    protected $messageTemplates = [
        self::INVALID_CONTROLLER => 'Der Controller %value% ist in der Applikation nicht vorhanden.'
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
    public function isValid($value)
    {
        $controller = $value;
        
        if (true == $this->controllerExists($controller)) {
            return true;
        }
        
        $this->error(self::INVALID_CONTROLLER);
        return false;
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

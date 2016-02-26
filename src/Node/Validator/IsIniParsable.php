<?php
namespace Node\Validator;

use Zend\Validator\AbstractValidator;
use Zend\Config\Reader\Ini;

/**
 * Validator checks if an Zend\Config\Ini-Object can be created from the given value.
 */
class IsIniParsable extends AbstractValidator
{

    const NOT_PARSABLE = 'not_parsable';

    /**
     * Error-Messages.
     *
     * @var array
     */
    protected $messageTemplates = [
        self::NOT_PARSABLE => 'Die übergebenen Daten entsprechen nicht dem INI-Format.'
    ];

    /**
     * Checks if the provided value can be read by Zend\Config\Ini.
     *
     * @param string $value            
     * @return boolean
     */
    protected function parseIni($value)
    {
        $iniReader = new Ini();
        try {
            $ini = $iniReader->fromString($value);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Zend\Validator\ValidatorInterface::isValid()
     */
    public function isValid($value)
    {
        if (false === $this->parseIni($value)) {
            $this->error(self::NOT_PARSABLE);
            return false;
        }
        
        return true;
    }
}

<?php
namespace Node\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Form to filter the displayed nodes in the backend.
 */
class FilterForm extends Form implements InputFilterProviderInterface
{

    public function getInputFilterSpecification()
    {
        return [
            'search' => [
                'required' => false,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ]
            ],
            'type' => [
                'required' => false
            ]
        ];
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Zend\Form\Element::init()
     */
    public function init()
    {
        $this->add([
            'type' => 'Text',
            'name' => 'search',
            'options' => [
                'label' => 'Search'
            ]
        ]);
        
        $this->add([
            'type' => 'Select',
            'name' => 'type',
            'options' => [
                'label' => 'Node-Type',
                'empty_option' => '',
                'value_options' => [
                    'content' => 'Content-Node',
                    'mvc' => 'MVC-Node',
                    'redirect' => 'Redirect-Node'
                ]
            ]
        ]);
        
        $this->add([
            'type' => 'Button',
            'name' => 'submit',
            'attributes' => [
                'type' => 'submit'
            ],
            'options' => [
                'label' => 'Apply'
            ]
        ]);
        
        $this->add([
            'type' => 'Button',
            'name' => 'reset',
            'attributes' => [
                'type' => 'submit'
            ],
            'options' => [
                'label' => 'Reset'
            ]
        ]);
    }
}

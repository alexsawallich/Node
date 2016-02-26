<?php
return [
    'controllers' => [
        'factories' => [
            'Node\Controller\Backend' => \Node\Controller\BackendControllerFactory::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            'NodeForm' => \Node\Form\NodeFormFactory::class,
            'NodeFilterForm' => \Node\Form\FilterFormFactory::class,
        ],
    ],
    'input_filters' => [
        'factories' => [
            'NodeInputFilter' => \Node\Form\NodeInputFilterFactory::class,
        ]
    ],
    'navigation' => [
        'admin' => [
            'node' => [
                'label' => 'Nodes',
                'route' => 'zfcadmin/node'
            ]
        ]
    ],
    'router' => [
        'router_class' => '\Node\Router\NodeRouter',
        'routes' => [
            'zfcadmin' => [
                'child_routes' => [
                    'node' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/nodes',
                            'defaults' => [
                                '__NAMESPACE__' => 'Node\Controller',
                                'controller' => 'Backend',
                                'action' => 'index'
                            ]
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'batch' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/batch',
                                    'defaults' => [
                                        'action' => 'batch'
                                    ]
                                ]
                            ],
                            'add' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/add',
                                    'defaults' => [
                                        'action' => 'add'
                                    ]
                                ]
                            ],
                            'edit' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/edit/:id',
                                    'defaults' => [
                                        'action' => 'edit'
                                    ],
                                    'constraints' => [
                                        'id' => '\d+'
                                    ]
                                ]
                            ],
                            'delete' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/delete[/:id]',
                                    'defaults' => [
                                        'action' => 'delete'
                                    ],
                                    'constraints' => [
                                        'id' => '\d+'
                                    ]
                                ]
                            ],
                            'load-actions' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/load-actions',
                                    'defaults' => [
                                        'action' => 'load-actions'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ],
    'service_manager' => [
        'factories' => [
            'NodeTable' => \Node\Model\NodeTableFactory::class,
            'NodeRouteCache' => \Node\Service\NodeRouteCacheFactory::class,
            'NodeOptions' => \Node\Options\NodeOptionsFactory::class,
            'NodeModelHydrator' => \Node\Model\NodeHydratorFactory::class,
            'NodeFormHydrator' => \Node\Form\NodeHydratorFactory::class,
            'NodeService' => \Node\Service\NodeServiceFactory::class,
            'NodeRouter' => \Node\Router\NodeRouterFactory::class,
            'NodeMvcListenerAggregate' => \Node\ListenerAggregate\MvcListenerAggregateFactory::class,
            'NodeServiceListenerAggregate' => \Node\ListenerAggregate\NodeServiceListenerAggregateFactory::class,
        ],
    ],
    'validators' => [
        'factories' => [
            'IsIniParsable' => \Node\Validator\IsIniParsableFactory::class,
            'IsValidController' => \Node\Validator\IsValidControllerFactory::class,
            'IsValidAction' => \Node\Validator\IsValidActionFactory::class,
        ]
    ],
    'view_manager' => [
        'template_path_stack' => [
            'Node' => __DIR__ . '/../view'
        ],
        'strategies' => [
            'ViewJsonStrategy'
        ]
    ]
];

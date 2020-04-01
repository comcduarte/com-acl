<?php
use Acl\Controller\AclController;

return [
    'router' => [
        'routes' => [
            'acl' => [
                'type' => Literal::class,
                'priority' => 1,
                'options' => [
                    'route' => '/acl',
                    'defaults' => [
                        'action' => 'index',
                        'controller' => Acl\Controller\AclController::class,
                    ],
                ],
                'may_terminate' => TRUE,
                'child_routes' => [
                    'config' => [
                        'type' => Segment::class,
                        'priority' => 100,
                        'options' => [
                            'route' => '/config[/:action]',
                            'defaults' => [
                                'action' => 'index',
                                'controller' => Acl\Controller\AclConfigController::class,
                            ],
                        ],
                    ],
                    'default' => [
                        'type' => Segment::class,
                        'priority' => -100,
                        'options' => [
                            'route' => '/[:action[/:uuid]]',
                            'defaults' => [
                                'action' => 'index',
                                'controller' => Acl\Controller\AclController::class,
                            ],
                        ],
                    ],
                ],
            ],
            
            'denied' => [
                'type' => Laminas\Router\Http\Literal::class,
                'options' => [
                    'route' => '/denied',
                    'defaults' => [
                        'controller' => Acl\Controller\IndexController::class,
                        'action' => 'denied',
                    ],
                ],
            ],
        ],
    ],
    'acl' => [
        'guest' => [
            'denied' => ['view'],
            'acl/config' => NULL,
        ],
        'member' => [
            'acl/config' => [],
            'acl/default' => NULL,
        ],
    ],
    'controllers' => [
        'factories' => [
            Acl\Controller\IndexController::class => Laminas\ServiceManager\Factory\InvokableFactory::class,
            Acl\Controller\AclController::class => Acl\Controller\Factory\AclControllerFactory::class,
            Acl\Controller\AclConfigController::class => Acl\Controller\Factory\AclConfigControllerFactory::class,
        ],
        'aliases' => [
            'index' => Acl\Controller\IndexController::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            Acl\Form\AclForm::class => Acl\Form\Factory\AclFormFactory::class,
        ],
    ],
    'navigation' => [
        'default' => [
            'acl' => [
                'label' => 'Acl',
                'route' => 'acl/default',
                'class' => 'dropdown',
                'order' => 10,
                'pages' => [
                    [
                        'label' => 'Add New Acl',
                        'route' => 'acl/default',
                        'action' => 'create',
                    ],
                    [
                        'label' => 'List Acls',
                        'route' => 'acl/default',
                    ],
                ],
            ],
            'settings' => [
                'label' => 'Settings',
                'pages' => [
                    'acl' => [
                        'label' => 'Acl Settings',
                        'route' => 'acl/config',
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'aliases' => [
            'acl-listener' => Acl\Listener\AclListener::class,
            'acl-service' => Acl\Service\AclService::class,
            'acl-model-adapter-config' => 'model-adapter-config',
        ],
        'factories' => [
            Acl\Listener\AclListener::class => Acl\Listener\Factory\AclListenerFactory::class,
            Acl\Service\AclService::class => Acl\Service\Factory\AclServiceFactory::class,
            'acl-model-adapter' => Acl\Service\Factory\AclModelAdapterFactory::class,
        ],
    ],
    'view_manager' => [
        'template_map' => [
            'acl/config' => __DIR__ . '/../view/acl/config/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
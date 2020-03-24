<?php
return [
    'router' => [
        'routes' => [
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
        ],
    ],
    'controllers' => [
        'factories' => [
            Acl\Controller\IndexController::class => Laminas\ServiceManager\Factory\InvokableFactory::class,
        ],
        'aliases' => [
            'index' => Acl\Controller\IndexController::class,
        ],
    ],
    'service_manager' => [
        'aliases' => [
            'acl-listener' => Acl\Listener\AclListener::class,
            'acl-service' => Acl\Service\AclService::class,
        ],
        'factories' => [
            Acl\Listener\AclListener::class => Acl\Listener\Factory\AclListenerFactory::class,
            Acl\Service\AclService::class => Acl\Service\Factory\AclServiceFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
<?php
/**
 * @package Next
 * @author Yury Ostapenko
 */

namespace Next;

use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\Router\Http\Literal;

return [
    'router' => [
        'routes' => [
            'next-generate' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/generate',
                    'defaults' => [
                        'controller' => Controller\GenerateController::class,
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\GenerateController::class => InvokableFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'next' => __DIR__ . '/../view',
        ],
    ],
];

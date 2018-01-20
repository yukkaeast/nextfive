<?php
/**
 * @package Next
 * @author Yury Ostapenko
 */

namespace Next;

use Zend\Router\Http\Segment;
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
            'next-get' => [
                'type'    => Segment::class,
                'options' => [
                    'route'       => '/get[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults'    => [
                        'controller' => Controller\GetController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'next' => __DIR__ . '/../view',
        ],
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ],
];

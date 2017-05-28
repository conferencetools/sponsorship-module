<?php
use ConferenceTools\Sponsorship\Controller;

return [
    'root' => [
        'type' => 'Segment',
        'options' => [
            'route' => '/',
            'defaults' => [
                'controller' => Controller\TaskController::class,
                'action' => 'index',
            ],
        ],
        'may_terminate' => true,
        'child_routes' => [
            'lead' => [
                'type' => \Zend\Mvc\Router\Http\Segment::class,
                'options' => [
                    'route' => 'lead/',
                    'defaults' => [
                        'controller' => Controller\LeadController::class
                    ]
                ],
                'child_routes' => [
                    'new' => [
                        'type' => \Zend\Mvc\Router\Http\Segment::class,
                        'options' => [
                            'route' => 'new',
                            'defaults' => [
                                'action' => 'newLead'
                            ]
                        ],
                        'may_terminate' => true,
                    ]
                ]
            ]
        ],
    ],
];

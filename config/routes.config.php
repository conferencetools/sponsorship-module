<?php
use ConferenceTools\Sponsorship\Controller;

return [
    'sponsorship' => [
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
                        'controller' => Controller\LeadController::class,
                        'action' => 'index',
                    ]
                ],
                'may_terminate' => true,
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
                    ],
                    'view' => [
                        'type' => \Zend\Mvc\Router\Http\Segment::class,
                        'options' => [
                            'route' => 'view/:leadId',
                            'defaults' => [
                                'action' => 'viewLead'
                            ]
                        ],
                        'may_terminate' => true,
                    ],
                ]
            ],
            'conversation' => [
                'type' => \Zend\Mvc\Router\Http\Segment::class,
                'options' => [
                    'route' => 'conversation/',
                    'defaults' => [
                        'controller' => Controller\ConversationController::class
                    ]
                ],
                'child_routes' => [
                    'start' => [
                        'type' => \Zend\Mvc\Router\Http\Segment::class,
                        'options' => [
                            'route' => 'start/:leadId',
                            'defaults' => [
                                'action' => 'start'
                            ]
                        ],
                        'may_terminate' => true,
                    ],
                    'reply' => [
                        'type' => \Zend\Mvc\Router\Http\Segment::class,
                        'options' => [
                            'route' => 'reply/:conversationId',
                            'defaults' => [
                                'action' => 'reply'
                            ]
                        ],
                        'may_terminate' => true,
                    ]
                ]
            ],
        ],
    ],
];

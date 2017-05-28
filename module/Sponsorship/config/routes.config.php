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
        ],
    ],
];

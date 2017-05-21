<?php

return [
    'root' => [
        'type' => 'Segment',
        'options' => [
            'route' => '/',
            'defaults' => [
                'controller' => '',
                'action' => 'index',
            ],
        ],
        'may_terminate' => true,
        'child_routes' => [
        ],
    ],
];

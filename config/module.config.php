<?php

return [
    'router' => [
        'routes' => require __DIR__ . '/routes.config.php',
    ],
    'navigation' => [
        'default' => require __DIR__ . '/navigation.config.php',
    ],
    'asset_manager' => require __DIR__ . '/asset.config.php',
    'service_manager' => [
        'factories' => [
            \ConferenceTools\Sponsorship\Service\Mailgun\Client::class =>
                \ConferenceTools\Sponsorship\Service\Mailgun\ClientFactory::class,
            \ConferenceTools\Sponsorship\Domain\Service\IncomingMessageHandler::class =>
                \ConferenceTools\Sponsorship\Service\Factory\Service\IncomingMessageHandler::class
        ],
        'abstract_factories' => [
            \Zend\Log\LoggerAbstractServiceFactory::class,
            \Zend\Navigation\Service\NavigationAbstractServiceFactory::class,
        ],
    ],
    'cli_commands' => [
        'factories' => [
            \ConferenceTools\Sponsorship\Cli\Command\RetrieveEvents::class =>
                \ConferenceTools\Sponsorship\Cli\Command\RetrieveEventsFactory::class
        ],
    ],
    'command_handlers' => [
        'factories' => [
            \ConferenceTools\Sponsorship\Domain\CommandHandler\Conversation::class =>
                \ConferenceTools\Sponsorship\Service\Factory\CommandHandler\Conversation::class,
            \ConferenceTools\Sponsorship\Domain\CommandHandler\Lead::class =>
                \ConferenceTools\Sponsorship\Service\Factory\CommandHandler\Lead::class,
        ],
    ],
    'process_managers' => [
        'factories' => [
            \ConferenceTools\Sponsorship\Domain\ProcessManager\Conversation::class =>
                \ConferenceTools\Sponsorship\Service\Factory\ProcessManager\Conversation::class,
        ],
    ],
    'event_listeners' => [
        'factories' => [
            \ConferenceTools\Sponsorship\EventListener\SendMail::class =>
                \ConferenceTools\Sponsorship\EventListener\SendMailFactory::class
        ],
    ],
    'projections' => [
        'factories' => [
            \ConferenceTools\Sponsorship\Domain\Projection\Task::class =>
                \ConferenceTools\Sponsorship\Service\Factory\Projection\Task::class,
            \ConferenceTools\Sponsorship\Domain\Projection\Conversation::class =>
                \ConferenceTools\Sponsorship\Service\Factory\Projection\Conversation::class,
            \ConferenceTools\Sponsorship\Domain\Projection\Lead::class =>
                \ConferenceTools\Sponsorship\Service\Factory\Projection\Lead::class,
            \ConferenceTools\Sponsorship\Domain\Projection\Mapper::class =>
                \ConferenceTools\Sponsorship\Service\Factory\Projection\Mapper::class,
        ],
    ],
    'command_subscriptions' => [
        \ConferenceTools\Sponsorship\Domain\Command\Conversation\EscalateReply::class =>
            \ConferenceTools\Sponsorship\Domain\CommandHandler\Conversation::class,
        \ConferenceTools\Sponsorship\Domain\Command\Conversation\EscalateResponse::class =>
            \ConferenceTools\Sponsorship\Domain\CommandHandler\Conversation::class,
        \ConferenceTools\Sponsorship\Domain\Command\Conversation\RecordMessage::class =>
            \ConferenceTools\Sponsorship\Domain\CommandHandler\Conversation::class,
        \ConferenceTools\Sponsorship\Domain\Command\Conversation\SendMessage::class =>
            \ConferenceTools\Sponsorship\Domain\CommandHandler\Conversation::class,
        \ConferenceTools\Sponsorship\Domain\Command\Conversation\StartWithLead::class =>
            \ConferenceTools\Sponsorship\Domain\CommandHandler\Conversation::class,
        \ConferenceTools\Sponsorship\Domain\Command\Conversation\StartWithMessage::class =>
            \ConferenceTools\Sponsorship\Domain\CommandHandler\Conversation::class,
        \ConferenceTools\Sponsorship\Domain\Command\Conversation\AssignToLead::class =>
            \ConferenceTools\Sponsorship\Domain\CommandHandler\Conversation::class,

        \ConferenceTools\Sponsorship\Domain\Command\Lead\AcquireLead::class =>
            \ConferenceTools\Sponsorship\Domain\CommandHandler\Lead::class,
    ],
    'domain_event_subscriptions' => require __DIR__ . '/domain_event_subscriptions.config.php',
    'controllers' => [
        'factories' => [
            \ConferenceTools\Sponsorship\Controller\TaskController::class =>
                \ConferenceTools\Sponsorship\Service\Factory\ControllerFactory::class,
            \ConferenceTools\Sponsorship\Controller\LeadController::class =>
                \ConferenceTools\Sponsorship\Service\Factory\ControllerFactory::class,
            \ConferenceTools\Sponsorship\Controller\ConversationController::class =>
                \ConferenceTools\Sponsorship\Controller\ConversationControllerFactory::class,
        ],
    ],
    'view_helpers' => [
        'invokables' => [
        ],
        'factories' => [
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => false,
        'display_exceptions' => false,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => [
            'email/outbound' => __DIR__ . '/../view/email/outbound.phtml',
            'sponsorship/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
            'sponsorship/task/index' => __DIR__ . '/../view/sponsorship/task/index.phtml',
            'sponsorship/lead/new-lead' => __DIR__ . '/../view/sponsorship/lead/new-lead.phtml',
            'sponsorship/lead/view-lead' => __DIR__ . '/../view/sponsorship/lead/view-lead.phtml',
            'sponsorship/lead/index' => __DIR__ . '/../view/sponsorship/lead/index.phtml',
            'sponsorship/conversation/reply' => __DIR__ . '/../view/sponsorship/conversation/reply.phtml',
        ],
        'controller_map' => [
            'ConferenceTools\Sponsorship\Controller' => 'sponsorship',
        ],
    ],
    'doctrine' => [
        'driver' => [
            'conferencetools_sponsorship_read_orm_driver' => [
                'class' =>'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Domain/ReadModel']
            ],
            'orm_default' => [
                'drivers' => [
                    'ConferenceTools\Sponsorship\Domain\ReadModel' => 'conferencetools_sponsorship_read_orm_driver',
                    'ConferenceTools\Sponsorship\Domain\ValueObject' => 'conferencetools_sponsorship_read_orm_driver',
                ],
            ],
        ],
    ],
    'log' => [
        'Log\\Application' => [
            'writers' => [
                [
                    'name' => 'syslog',
                ],
            ],
        ],
        'Log\\CommandBusLog'  => [
            'writers' => [
                [
                    'name' => 'syslog',
                ],
            ],
        ],
        'Log\\EventManagerLog'  => [
            'writers' => [
                [
                    'name' => 'syslog',
                ],
            ],
        ],
    ],
    'message_handlers' => [
        'CommandHandlerManager' => [
            'logger' => 'Log\\Application',
        ],
        'ProjectionManager' => [
            'logger' => 'Log\\Application',
        ],
        'EventListenerManager' => [
            'logger' => 'Log\\Application',
        ],
        'EventSubscriberManager' => [
            'logger' => 'Log\\Application',
        ],
    ],
];

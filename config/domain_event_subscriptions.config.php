<?php
use ConferenceTools\Sponsorship\Domain\Projection;
use ConferenceTools\Sponsorship\Domain\ProcessManager;
use ConferenceTools\Sponsorship\Domain\Event;

return  [
    Event\Conversation\MessageReceived::class => [
        Projection\Task::class,
        ProcessManager\Conversation::class,
        Projection\Conversation::class,
        Projection\Mapper::class,
    ],
    Event\Conversation\MessageSent::class => [
        Projection\Task::class,
        ProcessManager\Conversation::class,
        Projection\Conversation::class,
        Projection\Mapper::class,
        \ConferenceTools\Sponsorship\EventListener\SendMail::class,
    ],
    Event\Conversation\ReplyOutstanding::class => [
        Projection\Task::class,
    ],
    Event\Conversation\ReplyTimeout::class => [
        ProcessManager\Conversation::class,
    ],
    Event\Conversation\ResponseOutstanding::class => [
        Projection\Task::class,
    ],
    Event\Conversation\ResponseTimeout::class => [
        ProcessManager\Conversation::class,
    ],
    Event\Conversation\Started::class => [
        Projection\Conversation::class,
    ],
    Event\Conversation\StartedWithLead::class => [
        Projection\Conversation::class,
        Projection\Lead::class,
        Projection\Task::class,
        Projection\Mapper::class,
    ],
    Event\Lead\LeadAcquired::class => [
        Projection\Lead::class,
        Projection\Task::class,
        Projection\Mapper::class,
    ],

    Event\Conversation\AssignedToLead::class => [
        Projection\Mapper::class,
        Projection\Lead::class,
        Projection\Conversation::class,
        Projection\Task::class,
    ]
];
<?php

namespace ConferenceTools\Sponsorship\Domain\EventListener;

use Carnage\Cqrs\MessageBus\MessageBusInterface;
use Carnage\Cqrs\MessageHandler\AbstractMethodNameMessageHandler;
use ConferenceTools\Sponsorship\Domain\Command\Conversation\StartWithLead;
use ConferenceTools\Sponsorship\Domain\Event\Lead\LeadAcquired;

class StartConversation extends AbstractMethodNameMessageHandler
{
    private $commandBus;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    protected function handleLeadAcquired(LeadAcquired $event)
    {
        $this->commandBus->dispatch(new StartWithLead($event->getId()));
    }
}
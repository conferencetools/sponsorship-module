<?php

namespace ConferenceTools\Sponsorship\Domain\Model\Conversation;

use Carnage\Cqrs\Aggregate\AbstractAggregate;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\MessageReceived;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\StartedWithLead;
use ConferenceTools\Sponsorship\Domain\ValueObject\Contact;
use ConferenceTools\Sponsorship\Domain\ValueObject\Message;

class Conversation extends AbstractAggregate
{
    private $id;
    private $leadId;
    private $messages;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public static function fromNewLead($id, $leadId)
    {
        $event = new StartedWithLead($id, $leadId);

        $instance = new self();
        $instance->apply($event);

        return $instance;
    }

    protected function applyStartedWithLead(StartedWithLead $event)
    {
        $this->id = $event->getId();
        $this->leadId = $event->getLeadId();
    }

    public function messageReceived(Contact $from, Message $message)
    {
        $event = new MessageReceived($this->id, $from, $message);
        $this->apply($event);
    }

    protected function applyMessageReceived(MessageReceived $event)
    {
        $this->messages[] = InboundMessage::fromMessageReceivedEvent($event);
    }
}

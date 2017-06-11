<?php

namespace ConferenceTools\Sponsorship\Domain\Model\Conversation;

use Carnage\Cqrs\Aggregate\AbstractAggregate;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\MessageReceived;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\MessageSent;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\ReplyOutstanding;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\ResponseOutstanding;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\ResponseTimeout;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\StartedWithLead;
use ConferenceTools\Sponsorship\Domain\ValueObject\Contact;
use ConferenceTools\Sponsorship\Domain\ValueObject\Message;

class Conversation extends AbstractAggregate
{
    private $id;
    private $leadId;
    private $messages;
    private $numberOfChaseMessages = 0;

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
        $this->numberOfChaseMessages = 0;
    }

    public function messageSent(Message $message)
    {
        $event = new MessageSent($this->id, $message);
        $this->apply($event);
    }

    protected function applyMessageSent(MessageSent $event)
    {
        $this->messages[] = OutboundMessage::fromMessageSentEvent($event);
    }

    public function escalateReply()
    {
        $event = new ReplyOutstanding($this->id, $this->numberOfChaseMessages);
        $this->apply($event);
    }

    public function escalateResponse()
    {
        $event = new ResponseOutstanding($this->id, $this->numberOfChaseMessages);
        $this->apply($event);
    }

    protected function applyResponseOutstanding(ResponseOutstanding $event)
    {
        $this->numberOfChaseMessages ++;
    }
}

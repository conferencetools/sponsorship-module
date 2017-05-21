<?php

namespace ConferenceTools\Sponsorship\Domain\Model\Conversation;

use ConferenceTools\Sponsorship\Domain\Event\Conversation\MessageReceived;
use ConferenceTools\Sponsorship\Domain\ValueObject\Contact;
use ConferenceTools\Sponsorship\Domain\ValueObject\Message;

class InboundMessage
{
    private $from;
    private $message;

    public function __construct(Contact $from, Message $message)
    {
        $this->from = $from;
        $this->message = $message;
    }

    public static function fromMessageReceivedEvent(MessageReceived $event)
    {
        $instance = new self($event->getFrom(), $event->getMessage());
        return $instance;
    }
}
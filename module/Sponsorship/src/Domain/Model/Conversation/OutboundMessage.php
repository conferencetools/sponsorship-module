<?php

namespace ConferenceTools\Sponsorship\Domain\Model\Conversation;

use ConferenceTools\Sponsorship\Domain\Event\Conversation\MessageSent;
use ConferenceTools\Sponsorship\Domain\ValueObject\Message;

class OutboundMessage
{
    private $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public static function fromMessageSentEvent(MessageSent $event)
    {
        $instance = new self($event->getMessage());
        return $instance;
    }
}
<?php

namespace ConferenceTools\Sponsorship\Domain\Command\Conversation;

use Carnage\Cqrs\Command\CommandInterface;
use ConferenceTools\Sponsorship\Domain\ValueObject\Contact;
use ConferenceTools\Sponsorship\Domain\ValueObject\Message;

class RecordMessage implements CommandInterface
{
    private $from;
    private $message;
    private $conversationId;

    public function __construct(string $conversationId, Contact $from, Message $message)
    {
        $this->from = $from;
        $this->message = $message;
        $this->conversationId = $conversationId;
    }

    /**
     * @return string
     */
    public function getConversationId(): string
    {
        return $this->conversationId;
    }

    public function getFrom(): Contact
    {
        return $this->from;
    }

    /**
     * @return Message
     */
    public function getMessage(): Message
    {
        return $this->message;
    }
}
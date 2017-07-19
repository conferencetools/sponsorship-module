<?php

namespace ConferenceTools\Sponsorship\Domain\Command\Conversation;

use Carnage\Cqrs\Command\CommandInterface;
use ConferenceTools\Sponsorship\Domain\ValueObject\Contact;
use ConferenceTools\Sponsorship\Domain\ValueObject\Message;

class StartWithMessage implements CommandInterface
{
    private $from;
    private $message;

    public function __construct(Contact $from, Message $message)
    {
        $this->from = $from;
        $this->message = $message;
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
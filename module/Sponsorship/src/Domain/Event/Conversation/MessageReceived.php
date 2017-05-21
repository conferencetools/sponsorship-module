<?php


namespace ConferenceTools\Sponsorship\Domain\Event\Conversation;


use Carnage\Cqrs\Event\EventInterface;
use ConferenceTools\Sponsorship\Domain\ValueObject\Contact;
use ConferenceTools\Sponsorship\Domain\ValueObject\Message;

class MessageReceived implements EventInterface
{
    private $id;
    private $from;
    private $message;

    public function __construct(string $id, Contact $from, Message $message)
    {
        $this->id = $id;
        $this->from = $from;
        $this->message = $message;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFrom(): Contact
    {
        return $this->from;
    }

    public function getMessage(): Message
    {
        return $this->message;
    }
}

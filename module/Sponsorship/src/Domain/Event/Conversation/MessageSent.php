<?php


namespace ConferenceTools\Sponsorship\Domain\Event\Conversation;


use Carnage\Cqrs\Event\EventInterface;
use ConferenceTools\Sponsorship\Domain\ValueObject\Message;

class MessageSent implements EventInterface
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var Message
     */
    private $message;

    /**
     * MessageSent constructor.
     * @param $id
     * @param Message $message
     */
    public function __construct(string $id, Message $message)
    {
        $this->id = $id;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return Message
     */
    public function getMessage(): Message
    {
        return $this->message;
    }
}

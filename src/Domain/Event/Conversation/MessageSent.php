<?php


namespace ConferenceTools\Sponsorship\Domain\Event\Conversation;

use JMS\Serializer\Annotation as JMS;
use Carnage\Cqrs\Event\EventInterface;
use ConferenceTools\Sponsorship\Domain\ValueObject\Message;

class MessageSent implements EventInterface
{
    /**
     * @JMS\Type("string")
     * @var string
     */
    private $id;
    /**
     * @JMS\Type("ConferenceTools\Sponsorship\Domain\ValueObject\Message")
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

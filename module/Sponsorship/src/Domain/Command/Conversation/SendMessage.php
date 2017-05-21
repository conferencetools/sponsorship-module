<?php


namespace ConferenceTools\Sponsorship\Domain\Command\Conversation;


use Carnage\Cqrs\Command\CommandInterface;
use ConferenceTools\Sponsorship\Domain\ValueObject\Message;

class SendMessage implements CommandInterface
{
    private $conversationId;
    private $message;

    /**
     * SendMessage constructor.
     * @param $conversationId
     * @param $message
     */
    public function __construct(string $conversationId, Message $message)
    {
        $this->conversationId = $conversationId;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getConversationId(): string
    {
        return $this->conversationId;
    }

    /**
     * @return Message
     */
    public function getMessage(): Message
    {
        return $this->message;
    }
}

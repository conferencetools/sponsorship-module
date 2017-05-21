<?php

namespace ConferenceTools\Sponsorship\Domain\Command\Conversation;

use Carnage\Cqrs\Command\CommandInterface;

class EscalateReply implements CommandInterface
{
    /**
     * @var string
     */
    private $conversationId;

    /**
     * EscalateReply constructor.
     * @param $id
     */
    public function __construct(string $conversationId)
    {
        $this->conversationId = $conversationId;
    }

    /**
     * @return string
     */
    public function getConversationId(): string
    {
        return $this->conversationId;
    }
}
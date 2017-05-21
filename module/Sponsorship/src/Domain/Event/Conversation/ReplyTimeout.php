<?php

namespace ConferenceTools\Sponsorship\Domain\Event\Conversation;

use Carnage\Cqrs\Event\EventInterface;

class ReplyTimeout implements EventInterface
{
    /**
     * @var string
     */
    private $conversationId;

    /**
     * ReplyTimeout constructor.
     * @param $conversationId
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

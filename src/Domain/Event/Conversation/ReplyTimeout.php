<?php

namespace ConferenceTools\Sponsorship\Domain\Event\Conversation;

use Carnage\Cqrs\Event\EventInterface;
use JMS\Serializer\Annotation as JMS;

class ReplyTimeout implements EventInterface
{
    /**
     * @JMS\Type("string")
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

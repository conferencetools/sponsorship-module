<?php

namespace ConferenceTools\Sponsorship\Domain\Command\Conversation;

use Carnage\Cqrs\Command\CommandInterface;

class AssignToLead implements CommandInterface
{
    /**
     * @var string
     */
    private $leadId;

    /**
     * @var string
     */
    private $conversationId;

    public function __construct(string $conversationId, string $leadId)
    {
        $this->leadId = $leadId;
        $this->conversationId = $conversationId;
    }

    public function getLeadId(): string
    {
        return $this->leadId;
    }

    /**
     * @return string
     */
    public function getConversationId(): string
    {
        return $this->conversationId;
    }
}

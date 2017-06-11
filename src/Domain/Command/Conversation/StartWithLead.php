<?php

namespace ConferenceTools\Sponsorship\Domain\Command\Conversation;

use Carnage\Cqrs\Command\CommandInterface;

class StartWithLead implements CommandInterface
{
    private $leadId;

    public function __construct(string $leadId)
    {
        $this->leadId = $leadId;
    }

    public function getLeadId(): string
    {
        return $this->leadId;
    }
}

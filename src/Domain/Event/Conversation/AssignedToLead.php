<?php

namespace ConferenceTools\Sponsorship\Domain\Event\Conversation;

use Carnage\Cqrs\Event\EventInterface;
use JMS\Serializer\Annotation as JMS;

class AssignedToLead implements EventInterface
{
    /**
     * @var string
     * @JMS\Type("string")
     */
    private $id;

    /**
     * @var string
     * @JMS\Type("string")
     */
    private $leadId;

    /**
     * AssignedToLead constructor.
     * @param $id
     * @param $leadId
     */
    public function __construct(string $id, string $leadId)
    {
        $this->id = $id;
        $this->leadId = $leadId;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLeadId(): string
    {
        return $this->leadId;
    }
}
<?php

namespace ConferenceTools\Sponsorship\Domain\Event\Conversation;

use Carnage\Cqrs\Event\EventInterface;

class StartedWithLead implements EventInterface
{
    private $id;
    private $leadId;

    /**
     * StartedWithLead constructor.
     * @param $id
     * @param $leadId
     */
    public function __construct($id, $leadId)
    {
        $this->id = $id;
        $this->leadId = $leadId;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getLeadId()
    {
        return $this->leadId;
    }
}
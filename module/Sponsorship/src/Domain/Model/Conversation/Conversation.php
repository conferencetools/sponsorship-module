<?php

namespace ConferenceTools\Sponsorship\Domain\Model\Conversation;

use Carnage\Cqrs\Aggregate\AbstractAggregate;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\StartedWithLead;

class Conversation extends AbstractAggregate
{
    private $id;
    private $leadId;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public static function fromNewLead($id, $leadId)
    {
        $event = new StartedWithLead($id, $leadId);

        $instance = new self();
        $instance->apply($event);

        return $instance;
    }

    protected function applyStartedWithLead(StartedWithLead $event)
    {
        $this->id = $event->getId();
        $this->leadId = $event->getLeadId();
    }
}

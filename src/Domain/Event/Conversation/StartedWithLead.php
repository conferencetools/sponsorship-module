<?php

namespace ConferenceTools\Sponsorship\Domain\Event\Conversation;

use Carnage\Cqrs\Event\EventInterface;
use JMS\Serializer\Annotation as JMS;

class StartedWithLead implements EventInterface
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
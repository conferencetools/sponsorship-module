<?php

namespace ConferenceTools\Sponsorship\Domain\Model\Lead;

use Carnage\Cqrs\Aggregate\AbstractAggregate;
use ConferenceTools\Sponsorship\Domain\Event\Lead\LeadAcquired;
use ConferenceTools\Sponsorship\Domain\ValueObject\Contact;

class Lead extends AbstractAggregate
{
    private $id;
    private $contacts = [];
    private $name;

    public function getId()
    {
        return $this->id;
    }

    public static function leadAcquired($id, $name, Contact $contact)
    {
        $event = new LeadAcquired($id, $name, $contact);

        $instance = new self();
        $instance->apply($event);

        return $instance;
    }

    protected function applyLeadAcquired(LeadAcquired $event)
    {
        $this->id = $event->getId();
        $this->name = $event->getName();
        $this->contacts[] = $event->getContact();
    }
}
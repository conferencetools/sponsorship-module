<?php

namespace ConferenceTools\Sponsorship\Domain\Event\Lead;

use Carnage\Cqrs\Event\EventInterface;
use ConferenceTools\Sponsorship\Domain\ValueObject\Contact;

class LeadAcquired implements EventInterface
{
    private $id;
    private $name;
    private $contact;

    /**
     * LeadAcquired constructor.
     * @param $id
     * @param $name
     * @param Contact $contact
     */
    public function __construct($id, $name, Contact $contact)
    {
        $this->id = $id;
        $this->name = $name;
        $this->contact = $contact;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Contact
     */
    public function getContact(): Contact
    {
        return $this->contact;
    }
}

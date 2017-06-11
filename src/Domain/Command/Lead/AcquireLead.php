<?php

namespace ConferenceTools\Sponsorship\Domain\Command\Lead;

use Carnage\Cqrs\Command\CommandInterface;
use ConferenceTools\Sponsorship\Domain\ValueObject\Contact;

class AcquireLead implements CommandInterface
{
    private $name;
    private $contact;

    /**
     * AcquireLead constructor.
     * @param $name
     * @param $contact
     */
    public function __construct(string $name, Contact $contact)
    {
        $this->name = $name;
        $this->contact = $contact;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getContact()
    {
        return $this->contact;
    }
}
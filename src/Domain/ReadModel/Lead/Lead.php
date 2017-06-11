<?php

namespace ConferenceTools\Sponsorship\Domain\ReadModel\Lead;

use ConferenceTools\Sponsorship\Domain\ValueObject\Contact;
use ConferenceTools\Sponsorship\Domain\ValueObject\Message as MessageValueObject;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Lead
{
    /**
     * @var integer
     * @ORM\Id
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $leadId;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $companyName;

    /**
     * @ORM\Embedded(class="ConferenceTools\Sponsorship\Domain\ValueObject\Contact")
     * @var Contact
     */
    private $contact;

    /**
     * @var string[]
     * @ORM\Column("json")
     */
    private $conversations = [];

    /**
     * Lead constructor.
     * @param string $leadId
     * @param string $companyName
     * @param Contact $contact
     */
    public function __construct(string $leadId, string $companyName, Contact $contact)
    {
        $this->leadId = $leadId;
        $this->companyName = $companyName;
        $this->contact = $contact;
    }

    /**
     * @return string
     */
    public function getLeadId(): string
    {
        return $this->leadId;
    }

    /**
     * @return string
     */
    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    /**
     * @return Contact
     */
    public function getContact(): Contact
    {
        return $this->contact;
    }

    /**
     * @return \string[]
     */
    public function getConversations(): array
    {
        return $this->conversations;
    }

    public function addConversation(string $conversationId)
    {
        $this->conversations[] = $conversationId;
    }
}

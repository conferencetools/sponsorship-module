<?php

namespace ConferenceTools\Sponsorship\Domain\ReadModel\Mapping;

use ConferenceTools\Sponsorship\Domain\ValueObject\Contact;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Mapping
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
     * @ORM\Column(type="string", nullable=true)
     */
    private $leadId;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $conversationId;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $email;

    public static function ConversationToLead(string $conversationId, string $leadId): self
    {
        $instance = new static();
        $instance->leadId = $leadId;
        $instance->conversationId = $conversationId;

        return $instance;
    }

    public static function EmailToLead(string $leadId, string $email): self
    {
        $instance = new static();
        $instance->leadId = $leadId;
        $instance->email = $email;

        return $instance;
    }

    public static function EmailToConversation(string $conversationId, string $email): self
    {
        $instance = new static();
        $instance->conversationId = $conversationId;
        $instance->email = $email;

        return $instance;
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
    public function getConversationId(): string
    {
        return $this->conversationId;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }
}

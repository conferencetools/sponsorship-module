<?php

namespace ConferenceTools\Sponsorship\Domain\ReadModel\Conversation;

use ConferenceTools\Sponsorship\Domain\ValueObject\Contact;
use ConferenceTools\Sponsorship\Domain\ValueObject\Message as MessageValueObject;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Message
{
    const DIRECTION_INBOUND = 'inbound';
    const DIRECTION_OUTBOUND = 'outbound';
    /**
     * @var integer
     * @ORM\Id
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Conversation
     * @ORM\ManyToOne(targetEntity="ConferenceTools\Sponsorship\Domain\ReadModel\Conversation\Conversation", inversedBy="messages")
     * @ORM\JoinColumn(name="conversation_id", referencedColumnName="id")
     */
    private $conversation;

    /**
     * @var MessageValueObject
     * @ORM\Embedded(class="ConferenceTools\Sponsorship\Domain\ValueObject\Message")
     */
    private $message;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $direction;

    /**
     * @var Contact|null
     * @ORM\Column(type="json_object", nullable=true, name="fromx")
     */
    private $from;

    /**
     * Message constructor.
     * @param Conversation $conversation
     * @param MessageValueObject $message
     * @param string $direction
     */
    public function __construct(Conversation $conversation, MessageValueObject $message, string $direction, ?Contact $from)
    {
        $this->conversation = $conversation;
        $this->message = $message;
        $this->direction = $direction;
        $this->from = $from;
    }

    /**
     * @return MessageValueObject
     */
    public function getMessage(): MessageValueObject
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getDirection(): string
    {
        return $this->direction;
    }

    /**
     * @return Contact|null
     */
    public function getFrom(): ?Contact
    {
        return $this->from;
    }
}
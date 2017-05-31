<?php

namespace ConferenceTools\Sponsorship\Domain\ReadModel\Conversation;

use ConferenceTools\Sponsorship\Domain\ValueObject\Message as MessageValueObject;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Conversation
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
    private $conversationId;

    /**
     * @ORM\OneToMany(
     *     targetEntity="ConferenceTools\Sponsorship\Domain\ReadModel\Conversation\Message",
     *     mappedBy="conversation",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     *     )
     */
    private $messages;

    public function __construct(string $conversationId)
    {
        $this->conversationId = $conversationId;
        $this->messages = new ArrayCollection();
    }

    public function addMessage(MessageValueObject $message, string $direction)
    {
        $this->messages->add(new Message($this, $message, $direction));
    }

    /**
     * @return string
     */
    public function getConversationId(): string
    {
        return $this->conversationId;
    }

    /**
     * @return mixed
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }
}

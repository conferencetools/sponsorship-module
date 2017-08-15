<?php

namespace ConferenceTools\Sponsorship\Domain\ReadModel\Conversation;

use ConferenceTools\Sponsorship\Domain\ValueObject\Contact;
use ConferenceTools\Sponsorship\Domain\ValueObject\Message as MessageValueObject;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
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
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $messages;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $lead;

    public function __construct(string $conversationId)
    {
        $this->conversationId = $conversationId;
        $this->messages = new ArrayCollection();
    }

    public function forLead(string $lead)
    {
        $this->lead = $lead;
    }

    public function getLead(): string
    {
        return $this->lead;
    }

    public function hasLead(): bool
    {
        return !($this->lead === null);
    }

    public function addMessage(MessageValueObject $message, string $direction, ?Contact $from = null)
    {
        $this->messages->add(new Message($this, $message, $direction, $from));
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

    /**
     * @return Contact
     */
    public function getPrimaryContact()
    {
        $search = Criteria::create();
        $search->where(Criteria::expr()->eq('direction', Message::DIRECTION_INBOUND));
        $search->orderBy(['id' => Criteria::ASC]);
        $search->setMaxResults(1);

        $message = $this->messages->matching($search)->first();

        return $message ? $message->getFrom() : null;
    }
}

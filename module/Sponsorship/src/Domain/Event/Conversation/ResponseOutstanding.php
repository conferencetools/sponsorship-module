<?php

namespace ConferenceTools\Sponsorship\Domain\Event\Conversation;

use Carnage\Cqrs\Event\EventInterface;

class ResponseOutstanding implements EventInterface
{
    /**
     * @var
     */
    private $id;
    /**
     * @var int
     */
    private $numberOfChaseMessages;

    /**
     * ResponseOutstanding constructor.
     * @param $id
     * @param int $numberOfChaseMessages
     */
    public function __construct($id, $numberOfChaseMessages)
    {
        $this->id = $id;
        $this->numberOfChaseMessages = $numberOfChaseMessages;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getNumberOfChaseMessages(): int
    {
        return $this->numberOfChaseMessages;
    }
}
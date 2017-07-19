<?php

namespace ConferenceTools\Sponsorship\Domain\Event\Conversation;

use JMS\Serializer\Annotation as JMS;
use Carnage\Cqrs\Event\EventInterface;

class Started implements EventInterface
{
    /**
     * @var string
     * @JMS\Type("string")
     */
    private $id;

    /**
     * Started constructor.
     * @param $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}
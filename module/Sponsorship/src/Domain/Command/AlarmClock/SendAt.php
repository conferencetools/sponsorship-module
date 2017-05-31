<?php

namespace ConferenceTools\Sponsorship\Domain\Command\AlarmClock;

use JMS\Serializer\Annotation as JMS;
use Carnage\Cqrs\Command\CommandInterface;
use Carnage\Cqrs\MessageBus\MessageInterface;

class SendAt implements CommandInterface
{
    /**
     * @JMS\Type("Object")
     * @var MessageInterface
     */
    private $message;

    /**
     * @JMS\Type("DateTimeImmutable")
     * @var \DateTimeInterface
     */
    private $when;

    public function __construct(MessageInterface $message, \DateTimeImmutable $when)
    {
        $this->message = $message;
        $this->when = $when;
    }

    /**
     * @return MessageInterface
     */
    public function getMessage(): MessageInterface
    {
        return $this->message;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getWhen(): \DateTimeInterface
    {
        return $this->when;
    }
}
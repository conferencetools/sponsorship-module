<?php

namespace ConferenceTools\Sponsorship\Domain\Command\AlarmClock;

use Carnage\Cqrs\Command\CommandInterface;
use Carnage\Cqrs\MessageBus\MessageInterface;

class SendAt implements CommandInterface
{
    /**
     * @var MessageInterface
     */
    private $message;
    /**
     * @var \DateTimeInterface
     */
    private $when;

    public function __construct(MessageInterface $message, \DateTimeInterface $when)
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
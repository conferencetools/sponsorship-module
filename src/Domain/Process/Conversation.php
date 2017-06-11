<?php

namespace ConferenceTools\Sponsorship\Domain\Process;

use Carnage\Cqrs\Aggregate\AbstractAggregate;
use Carnage\Cqrs\Command\CommandInterface;
use Carnage\Cqrs\Process\NewProcessInterface;
use ConferenceTools\Sponsorship\Domain\Command\AlarmClock\SendAt;
use ConferenceTools\Sponsorship\Domain\Command\Conversation\EscalateReply;
use ConferenceTools\Sponsorship\Domain\Command\Conversation\EscalateResponse;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\MessageReceived;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\MessageSent;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\ReplyTimeout;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\ResponseTimeout;

/**
 * Experimental new process style.
 */
class Conversation extends AbstractAggregate implements NewProcessInterface
{
    private $id;
    private $replyOutstanding = false;
    private $responseOutstanding = false;

    public static function withId(string $id)
    {
        $instance = new self;
        $instance->id = $id;
        return $instance;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @TODO perhaps pass in the interval based on business rules.
     */
    public function messageReceived(MessageReceived $triggerEvent)
    {
        $this->apply($triggerEvent);
        $command = $this->createReplyTimeoutCommand();

        $this->apply($command);
    }

    protected function applyMessageReceived(MessageReceived $event)
    {
        //@TODO can we get this from somewhere else / somewhere better?
        $this->id = $event->getId();
        $this->replyOutstanding = true;
        $this->responseOutstanding = false;
    }

    public function messageSent(MessageSent $triggerEvent)
    {
        $this->apply($triggerEvent);
        $event = new ResponseTimeout($this->id);

        $when = (new \DateTimeImmutable())->add(new \DateInterval('P5D'));
        $command = new SendAt($event, $when);

        $this->apply($command);
    }

    protected function applyMessageSent(MessageSent $event)
    {
        //@TODO can we get this from somewhere else / somewhere better?
        $this->id = $event->getId();
        $this->replyOutstanding = false;
        $this->responseOutstanding = true;
    }

    public function replyTimeout()
    {
        if ($this->replyOutstanding) {
            $command = new EscalateReply($this->id);
            $this->apply($command);
        }
    }

    public function responseTimeout()
    {
        if ($this->responseOutstanding) {
            $command = new EscalateResponse($this->id);
            $this->apply($command);
            $this->apply($this->createReplyTimeoutCommand());
        }
    }

    /**
     * @return SendAt
     */
    private function createReplyTimeoutCommand(): SendAt
    {
        $event = new ReplyTimeout($this->id);

        $when = (new \DateTimeImmutable())->add(new \DateInterval('P3D'));
        $command = new SendAt($event, $when);
        return $command;
    }
}
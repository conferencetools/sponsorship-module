<?php

namespace ConferenceTools\Sponsorship\Domain\Process;

use Carnage\Cqrs\Aggregate\AbstractAggregate;
use Carnage\Cqrs\Command\CommandInterface;
use ConferenceTools\Sponsorship\Domain\Command\AlarmClock\SendAt;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\MessageReceived;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\ReplyTimeout;

/**
 * Experimental new process style.
 */
class Conversation extends AbstractAggregate
{
    private $id;
    private $replyOutstanding = false;

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
    public function messageReceived()
    {
        $event = new ReplyTimeout($this->id);

        $when = (new \DateTimeImmutable())->add(new \DateInterval('P3D'));
        $command = new SendAt($event, $when);

        $this->addCommand($command);
    }

    protected function applyMessageReceived(MessageReceived $event)
    {
        $this->replyOutstanding = true;
    }

    public function replyTimeout()
    {
        if ($this->replyOutstanding) {
            $command = new EscalateReply($this->id);
            $this->addCommand($command);
        }
    }

    /**
     * @TODO refactor base object + domain message to prevent this hackery being required.
     * @param CommandInterface $command
     * @param bool $new
     */
    public function addCommand(CommandInterface $command, $new = true)
    {
        $burn = function () use ($command, $new) {
            $this->version++;

            if ($new) {
                $this->uncommittedEvents[$this->version] = $command;
            }
        };

        ($burn->bindTo($this, parent::class))();
    }
}
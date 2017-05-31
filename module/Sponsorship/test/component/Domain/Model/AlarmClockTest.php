<?php

namespace ConferenceTools\Sponsorship\Domain\Model;

use ConferenceTools\Sponsorship\Testing\AbstractBusTest;
use ConferenceTools\Sponsorship\Domain\Command\AlarmClock\SendAt;
use ConferenceTools\Sponsorship\Domain\Command\AlarmClock\WakeUp;
use ConferenceTools\Sponsorship\Domain\CommandHandler\AlarmClock as AlarmClockCommandHandler;

class AlarmClockTest extends AbstractBusTest
{
    protected $modelClass = AlarmClock::class;

    public function test_it_capture_the_delayed_message()
    {
        $command = new SendAt(new WakeUp(), new \DateTimeImmutable());

        $sut = new AlarmClockCommandHandler($this->repository);
        $this->setupLogger($sut);

        $sut->handle($command);

        $event = $this->messageBus->messages[0]->getEvent();
        self::assertSame($command, $event);
    }

    public function test_it_sends_the_delayed_message()
    {
        $when = new \DateTimeImmutable();
        $deferredMessage = new WakeUp();
        $this->given(AlarmClock::class, AlarmClock::makeId($when), [new SendAt($deferredMessage, $when)]);

        $sut = new AlarmClockCommandHandler($this->repository);
        $this->setupLogger($sut);

        $command = new WakeUp();
        $sut->handle($command);

        $event = $this->messageBus->messages[0]->getEvent();
        self::assertSame($deferredMessage, $event);
    }

    public function test_it_doesnt_send_future_messages()
    {
        $when = (new \DateTimeImmutable())->add(new \DateInterval('PT1M'));
        $deferredMessage = new WakeUp();
        $this->given(AlarmClock::class, AlarmClock::makeId($when), [new SendAt($deferredMessage, $when)]);

        $sut = new AlarmClockCommandHandler($this->repository);
        $this->setupLogger($sut);

        $command = new WakeUp();
        $sut->handle($command);

        self::assertEmpty($this->messageBus->messages);
    }

    public function test_it_doesnt_send_messages_if_there_are_none()
    {
        $sut = new AlarmClockCommandHandler($this->repository);
        $this->setupLogger($sut);

        $command = new WakeUp();
        $sut->handle($command);

        self::assertEmpty($this->messageBus->messages);
    }
}
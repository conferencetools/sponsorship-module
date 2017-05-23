<?php

namespace ConferenceTools\Sponsorship\Domain\Process;

use ConferenceTools\Sponsorship\Domain\Command\AlarmClock\SendAt;
use ConferenceTools\Sponsorship\Domain\Command\Conversation\EscalateReply;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\MessageReceived;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\MessageSent;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\ReplyTimeout;
use ConferenceTools\Sponsorship\Domain\ProcessManager\Conversation as ConversationProcessManager;
use ConferenceTools\Sponsorship\Domain\ValueObject\Contact;
use ConferenceTools\Sponsorship\Domain\ValueObject\Message;
use ConferenceTools\Sponsorship\Testing\AbstractBusTest;

class ConversationTest extends AbstractBusTest
{
    protected $modelClass = Conversation::class;

    public function test_it_should_delay_a_reply_timeout_message_when_a_message_is_received()
    {
        $sut = new ConversationProcessManager($this->repository, $this->idGenerator);
        $this->setupLogger($sut);

        $event = new MessageReceived(
            '1',
            new Contact('Helen', 'helen@sponsors.com', '01234 654098'),
            new Message('Subject', 'Body')
        );

        $sut->handle($event);

        self::assertCount(2, $this->messageBus->messages);
        $domainMessage = $this->messageBus->messages[0]->getEvent();

        self::assertInstanceOf(SendAt::class, $domainMessage);
        self::assertInstanceOf(ReplyTimeout::class, $domainMessage->getMessage());
    }

    public function test_it_should_escalate_when_a_reply_timeout_occurs()
    {
        $previousEvents = [
            new MessageReceived(
                '1',
                new Contact('Helen', 'helen@sponsors.com', '01234 654098'),
                new Message('Subject', 'Body')
            ),
        ];
        $this->given(Conversation::class, '1', $previousEvents);

        $sut = new ConversationProcessManager($this->repository, $this->idGenerator);
        $this->setupLogger($sut);

        $event = new ReplyTimeout('1');

        $sut->handle($event);

        self::assertCount(2, $this->messageBus->messages);
        $domainMessage = $this->messageBus->messages[0]->getEvent();

        self::assertInstanceOf(EscalateReply::class, $domainMessage);
    }

    public function test_it_should_not_escalate_when_a_reply_has_been_sent()
    {
        $previousEvents = [
            new MessageReceived(
                '1',
                new Contact('Helen', 'helen@sponsors.com', '01234 654098'),
                new Message('Subject', 'Body')
            ),
            new MessageSent('1', new Message('Re: Subject', 'Body'))
        ];

        $this->given(Conversation::class, '1', $previousEvents);

        $sut = new ConversationProcessManager($this->repository, $this->idGenerator);
        $this->setupLogger($sut);

        $event = new ReplyTimeout('1');

        $sut->handle($event);

        self::assertCount(1, $this->messageBus->messages);
        $domainMessage = $this->messageBus->messages[0]->getEvent();

        self::assertInstanceOf(ReplyTimeout::class, $domainMessage);
    }
}

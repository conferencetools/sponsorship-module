<?php

namespace ConferenceTools\Sponsorship\Domain\Process;

use Carnage\Cqrs\Testing\AbstractBusTest;
use ConferenceTools\Sponsorship\Domain\Command\Conversation\SendAt;
use ConferenceTools\Sponsorship\Domain\Command\Conversation\EscalateReply;
use ConferenceTools\Sponsorship\Domain\Command\Conversation\EscalateResponse;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\MessageReceived;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\MessageSent;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\ReplyTimeout;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\ResponseTimeout;
use ConferenceTools\Sponsorship\Domain\ProcessManager\Conversation as ConversationProcessManager;
use ConferenceTools\Sponsorship\Domain\ValueObject\Contact;
use ConferenceTools\Sponsorship\Domain\ValueObject\Message;

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
        $domainMessage = $this->messageBus->messages[1]->getEvent();

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

        self::assertCount(1, $this->messageBus->messages);
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

        self::assertCount(0, $this->messageBus->messages);
    }

    public function test_it_should_delay_a_response_timeout_when_a_message_is_sent()
    {
        $sut = new ConversationProcessManager($this->repository, $this->idGenerator);
        $this->setupLogger($sut);

        $event = new MessageSent(
            '1',
            new Message('Subject', 'Body')
        );

        $sut->handle($event);

        self::assertCount(2, $this->messageBus->messages);
        $domainMessage = $this->messageBus->messages[1]->getEvent();

        self::assertInstanceOf(SendAt::class, $domainMessage);
        self::assertInstanceOf(ResponseTimeout::class, $domainMessage->getMessage());
    }

    public function test_it_should_escalate_when_a_response_timeout_occurs_and_expect_a_reply()
    {
        $previousEvents = [
            new MessageSent(
                '1',
                new Message('Subject', 'Body')
            ),
        ];
        $this->given(Conversation::class, '1', $previousEvents);

        $sut = new ConversationProcessManager($this->repository, $this->idGenerator);
        $this->setupLogger($sut);

        $event = new ResponseTimeout('1');

        $sut->handle($event);

        self::assertCount(2, $this->messageBus->messages);
        $domainMessage = $this->messageBus->messages[0]->getEvent();
        self::assertInstanceOf(EscalateResponse::class, $domainMessage);

        $domainMessage = $this->messageBus->messages[1]->getEvent();
        self::assertInstanceOf(SendAt::class, $domainMessage);
    }
}

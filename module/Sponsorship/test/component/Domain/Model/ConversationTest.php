<?php

namespace ConferenceTools\Sponsorship\Domain\Model;

use ConferenceTools\Sponsorship\Domain\Command\Conversation\RecordMessage;
use ConferenceTools\Sponsorship\Domain\Command\Conversation\SendMessage;
use ConferenceTools\Sponsorship\Domain\Command\Conversation\StartWithLead;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\MessageReceived;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\MessageSent;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\StartedWithLead;
use ConferenceTools\Sponsorship\Domain\Model\Conversation\Conversation;
use ConferenceTools\Sponsorship\Domain\CommandHandler\Conversation as ConversationCommandHandler;
use ConferenceTools\Sponsorship\Domain\ValueObject\Contact;
use ConferenceTools\Sponsorship\Domain\ValueObject\Message;
use ConferenceTools\Sponsorship\Testing\AbstractBusTest;

class ConversationTest extends AbstractBusTest
{
    protected $modelClass = Conversation::class;

    public function testStartWithLead()
    {
        $sut = new ConversationCommandHandler($this->repository, $this->idGenerator);
        $this->setupLogger($sut);

        $command = new StartWithLead('LeadId');

        $sut->handle($command);

        self::assertCount(1, $this->messageBus->messages);
        $domainMessage = $this->messageBus->messages[0]->getEvent();

        self::assertInstanceOf(StartedWithLead::class, $domainMessage);
        self::assertEquals(1, $domainMessage->getId());
        self::assertEquals('LeadId', $domainMessage->getLeadId());
    }

    public function testMessageReceived()
    {
        $this->given(Conversation::class, '1', [new StartedWithLead('1', 'LeadId')]);

        $sut = new ConversationCommandHandler($this->repository, $this->idGenerator);
        $this->setupLogger($sut);

        $contact = new Contact('Jo', 'jo@sponsor.com', '01234 678134');
        $message = new Message('Re:Sponsorship', 'Thanks');
        $command = new RecordMessage('1', $contact, $message);

        $sut->handle($command);

        self::assertCount(1, $this->messageBus->messages);
        $domainMessage = $this->messageBus->messages[0]->getEvent();

        self::assertInstanceOf(MessageReceived::class, $domainMessage);
        self::assertSame($message, $domainMessage->getMessage());
        self::assertSame($contact, $domainMessage->getFrom());
        self::assertEquals('1', $domainMessage->getId());
    }

    public function testMessageSent()
    {
        $this->given(Conversation::class, '1', [new StartedWithLead('1', 'LeadId')]);

        $sut = new ConversationCommandHandler($this->repository, $this->idGenerator);
        $this->setupLogger($sut);

        $message = new Message('Re:Sponsorship', 'Thanks');
        $command = new SendMessage('1', $message);

        $sut->handle($command);

        self::assertCount(1, $this->messageBus->messages);
        $domainMessage = $this->messageBus->messages[0]->getEvent();

        self::assertInstanceOf(MessageSent::class, $domainMessage);
        self::assertSame($message, $domainMessage->getMessage());
        self::assertEquals('1', $domainMessage->getId());
    }
}

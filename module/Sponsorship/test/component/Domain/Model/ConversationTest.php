<?php

namespace ConferenceTools\Sponsorship\Domain\Model;

use Carnage\Cqrs\Aggregate\Identity\GeneratorInterface;
use Carnage\Cqrs\Event\DomainMessage;
use Carnage\Cqrs\MessageBus\MessageBusInterface;
use Carnage\Cqrs\MessageBus\MessageInterface;
use Carnage\Cqrs\Persistence\EventStore\InMemoryEventStore;
use Carnage\Cqrs\Persistence\Repository\AggregateRepository;
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
use PHPUnit\Framework\TestCase;
use Zend\Log\Logger;
use Zend\Log\Writer\Noop;

class ConversationTest extends TestCase
{
    public function testStartWithLead()
    {
        $idGenerator = $this->makeIdGenerator();
        $messageBus = $this->makeMessageBus();
        $eventStore = new InMemoryEventStore();

        $repository = $this->makeRepository($messageBus, $eventStore);
        $sut = $this->makeCommandHandler($repository, $idGenerator);

        $command = new StartWithLead('LeadId');

        $sut->handle($command);

        self::assertCount(1, $messageBus->messages);
        $domainMessage = $messageBus->messages[0]->getEvent();

        self::assertInstanceOf(StartedWithLead::class, $domainMessage);
        self::assertEquals(1, $domainMessage->getId());
        self::assertEquals('LeadId', $domainMessage->getLeadId());
    }

    public function testMessageReceived()
    {
        $idGenerator = $this->makeIdGenerator();
        $messageBus = $this->makeMessageBus();
        $eventStore = $this->makeEventStore(Conversation::class, '1', [new StartedWithLead('1', 'LeadId')]);

        $repository = $this->makeRepository($messageBus, $eventStore);
        $sut = $this->makeCommandHandler($repository, $idGenerator);

        $contact = new Contact('Jo', 'jo@sponsor.com', '01234 678134');
        $message = new Message('Re:Sponsorship', 'Thanks');
        $command = new RecordMessage('1', $contact, $message);

        $sut->handle($command);

        self::assertCount(1, $messageBus->messages);
        $domainMessage = $messageBus->messages[0]->getEvent();

        self::assertInstanceOf(MessageReceived::class, $domainMessage);
        self::assertSame($message, $domainMessage->getMessage());
        self::assertSame($contact, $domainMessage->getFrom());
        self::assertEquals('1', $domainMessage->getId());
    }

    public function testMessageSent()
    {
        $idGenerator = $this->makeIdGenerator();
        $messageBus = $this->makeMessageBus();
        $eventStore = $this->makeEventStore(Conversation::class, '1', [new StartedWithLead('1', 'LeadId')]);

        $repository = $this->makeRepository($messageBus, $eventStore);
        $sut = $this->makeCommandHandler($repository, $idGenerator);

        $message = new Message('Re:Sponsorship', 'Thanks');
        $command = new SendMessage('1', $message);

        $sut->handle($command);

        self::assertCount(1, $messageBus->messages);
        $domainMessage = $messageBus->messages[0]->getEvent();

        self::assertInstanceOf(MessageSent::class, $domainMessage);
        self::assertSame($message, $domainMessage->getMessage());
        self::assertEquals('1', $domainMessage->getId());
    }

    /**
     * @return mixed
     */
    private function makeIdGenerator()
    {
        return new class implements GeneratorInterface
        {
            public $lastId = 0;

            public function generateIdentity()
            {
                $this->lastId++;
                return (string)$this->lastId;
            }
        };
    }

    /**
     * @return mixed
     */
    private function makeMessageBus()
    {
        $messageBus = new class implements MessageBusInterface
        {
            public $messages;

            public function dispatch(MessageInterface $message)
            {
                $this->messages[] = $message;
            }
        };
        return $messageBus;
    }

    /**
     * @param $messageBus
     * @return AggregateRepository
     */
    private function makeRepository($messageBus, $eventStore): AggregateRepository
    {
        $repository = new AggregateRepository(Conversation::class, $eventStore, $messageBus);
        return $repository;
    }

    /**
     * @param $repository
     * @param $idGenerator
     * @return ConversationCommandHandler
     */
    private function makeCommandHandler($repository, $idGenerator): ConversationCommandHandler
    {
        $logger = (new Logger())->addWriter(new Noop());

        $sut = new ConversationCommandHandler($repository, $idGenerator);
        $sut->setLogger($logger);
        return $sut;
    }

    /**
     * @return InMemoryEventStore
     */
    private function makeEventStore($class, $id, $events): InMemoryEventStore
    {
        $domainMessages = [];
        $version = 1;

        foreach ($events as $event) {
            $domainMessages[] = DomainMessage::recordEvent($class, $id, $version, $event);
            $version++;
        }

        $eventStore = new InMemoryEventStore();
        $eventStore->save($class, $id, $domainMessages);
        return $eventStore;
    }
}

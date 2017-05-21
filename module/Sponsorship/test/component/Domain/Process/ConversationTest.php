<?php

namespace ConferenceTools\Sponsorship\Domain\Process;

use Carnage\Cqrs\Aggregate\Identity\GeneratorInterface;
use Carnage\Cqrs\Event\DomainMessage;
use Carnage\Cqrs\MessageBus\MessageBusInterface;
use Carnage\Cqrs\MessageBus\MessageInterface;
use Carnage\Cqrs\Persistence\EventStore\InMemoryEventStore;
use Carnage\Cqrs\Persistence\Repository\AggregateRepository;
use ConferenceTools\Sponsorship\Domain\Command\AlarmClock\SendAt;
use ConferenceTools\Sponsorship\Domain\Command\Conversation\EscalateReply;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\MessageReceived;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\MessageSent;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\ReplyTimeout;
use ConferenceTools\Sponsorship\Domain\ProcessManager\Conversation as ConversationProcessManager;
use ConferenceTools\Sponsorship\Domain\ValueObject\Contact;
use ConferenceTools\Sponsorship\Domain\ValueObject\Message;
use PHPUnit\Framework\TestCase;
use Zend\Log\Logger;
use Zend\Log\Writer\Noop;

class ConversationTest extends TestCase
{
    public function test_it_should_delay_a_reply_timeout_message_when_a_message_is_received()
    {
        $idGenerator = $this->makeIdGenerator();
        $messageBus = $this->makeMessageBus();
        $eventStore = new InMemoryEventStore();

        $repository = $this->makeRepository($messageBus, $eventStore);
        $sut = $this->makeProcessManager($repository, $idGenerator);

        $event = new MessageReceived(
            '1',
            new Contact('Helen', 'helen@sponsors.com', '01234 654098'),
            new Message('Subject', 'Body')
        );

        $sut->handle($event);

        self::assertCount(2, $messageBus->messages);
        $domainMessage = $messageBus->messages[0]->getEvent();

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

        $idGenerator = $this->makeIdGenerator();
        $messageBus = $this->makeMessageBus();
        $eventStore = $this->makeEventStore(Conversation::class, '1', $previousEvents);

        $repository = $this->makeRepository($messageBus, $eventStore);
        $sut = $this->makeProcessManager($repository, $idGenerator);

        $event = new ReplyTimeout('1');

        $sut->handle($event);

        self::assertCount(2, $messageBus->messages);
        $domainMessage = $messageBus->messages[0]->getEvent();

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

        $idGenerator = $this->makeIdGenerator();
        $messageBus = $this->makeMessageBus();
        $eventStore = $this->makeEventStore(Conversation::class, '1', $previousEvents);

        $repository = $this->makeRepository($messageBus, $eventStore);
        $sut = $this->makeProcessManager($repository, $idGenerator);

        $event = new ReplyTimeout('1');

        $sut->handle($event);

        self::assertCount(1, $messageBus->messages);
        $domainMessage = $messageBus->messages[0]->getEvent();

        self::assertInstanceOf(ReplyTimeout::class, $domainMessage);
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
     * @return ConversationProcessManager
     */
    private function makeProcessManager($repository, $idGenerator): ConversationProcessManager
    {
        $logger = (new Logger())->addWriter(new Noop());

        $sut = new ConversationProcessManager($repository, $idGenerator);
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

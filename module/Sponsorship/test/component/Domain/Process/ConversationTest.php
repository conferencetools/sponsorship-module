<?php

namespace ConferenceTools\Sponsorship\Domain\Process;

use Carnage\Cqrs\Aggregate\Identity\GeneratorInterface;
use Carnage\Cqrs\Event\DomainMessage;
use Carnage\Cqrs\MessageBus\MessageBusInterface;
use Carnage\Cqrs\MessageBus\MessageInterface;
use Carnage\Cqrs\Persistence\EventStore\InMemoryEventStore;
use Carnage\Cqrs\Persistence\Repository\AggregateRepository;
use ConferenceTools\Sponsorship\Domain\AlarmClock\SendAt;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\MessageReceived;
use ConferenceTools\Sponsorship\Domain\ProcessManager\Conversation as ConversationProcessManager;
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
        $sut = $this->makeProcessManager($repository, $idGenerator);

        $event = new MessageReceived(
            '1',
            new Contact('Helen', 'helen@sponsors.com', '01234 654098'),
            new Message('Subject', 'Body')
        );

        $sut->handle($event);

        self::assertCount(1, $messageBus->messages);
        $domainMessage = $messageBus->messages[0];

        self::assertInstanceOf(SendAt::class, $domainMessage);
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

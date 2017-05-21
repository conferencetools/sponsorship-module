<?php

namespace ConferenceTools\Sponsorship\Domain\Model;

use Carnage\Cqrs\Aggregate\Identity\GeneratorInterface;
use Carnage\Cqrs\MessageBus\MessageBusInterface;
use Carnage\Cqrs\MessageBus\MessageInterface;
use Carnage\Cqrs\Persistence\EventStore\InMemoryEventStore;
use Carnage\Cqrs\Persistence\Repository\AggregateRepository;
use ConferenceTools\Sponsorship\Domain\Command\Conversation\StartWithLead;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\StartedWithLead;
use ConferenceTools\Sponsorship\Domain\Model\Conversation\Conversation;
use ConferenceTools\Sponsorship\Domain\CommandHandler\Conversation as ConversationCommandHandler;
use PHPUnit\Framework\TestCase;
use Zend\Log\Logger;
use Zend\Log\Writer\Noop;

class ConversationTest extends TestCase
{
    public function testLeadAcquired()
    {
        $idGenerator = new class implements GeneratorInterface {
            public $lastId = 0;
            public function generateIdentity()
            {
                $this->lastId++;
                return (string) $this->lastId;
            }
        };

        $messageBus = new class implements MessageBusInterface {
            public $messages;
            public function dispatch(MessageInterface $message)
            {
                $this->messages[] = $message;
            }
        };

        $eventStore = new InMemoryEventStore();
        $repository = new AggregateRepository(Conversation::class, $eventStore, $messageBus);
        $logger = (new Logger())->addWriter(new Noop());

        $sut = new ConversationCommandHandler($repository, $idGenerator);
        $sut->setLogger($logger);

        $command = new StartWithLead('LeadId');

        $sut->handle($command);

        self::assertCount(1, $messageBus->messages);
        $domainMessage = $messageBus->messages[0]->getEvent();

        self::assertInstanceOf(StartedWithLead::class, $domainMessage);
        self::assertEquals(1, $domainMessage->getId());
        self::assertEquals('LeadId', $domainMessage->getLeadId());
    }
}

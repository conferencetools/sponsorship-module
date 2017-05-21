<?php

namespace ConferenceTools\Sponsorship\Domain\Model;

use Carnage\Cqrs\Aggregate\Identity\GeneratorInterface;
use Carnage\Cqrs\MessageBus\MessageBusInterface;
use Carnage\Cqrs\MessageBus\MessageInterface;
use Carnage\Cqrs\Persistence\EventStore\InMemoryEventStore;
use Carnage\Cqrs\Persistence\Repository\AggregateRepository;
use ConferenceTools\Sponsorship\Domain\Command\Lead\AcquireLead;
use ConferenceTools\Sponsorship\Domain\CommandHandler\Lead as LeadCommandHandler;
use ConferenceTools\Sponsorship\Domain\Event\Lead\LeadAcquired;
use ConferenceTools\Sponsorship\Domain\Model\Lead\Lead;
use ConferenceTools\Sponsorship\Domain\ValueObject\Contact;
use PHPUnit\Framework\TestCase;
use Zend\Log\Logger;
use Zend\Log\Writer\Noop;

class LeadTest extends TestCase
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
        $repository = new AggregateRepository(Lead::class, $eventStore, $messageBus);
        $logger = (new Logger())->addWriter(new Noop());

        $sut = new LeadCommandHandler($repository, $idGenerator);
        $sut->setLogger($logger);

        $contact = new Contact('Zoe', 'zoe@awesomesponsor.com', '01233 123456');
        $command = new AcquireLead('Awesome sponsor', $contact);

        $sut->handle($command);

        self::assertCount(1, $messageBus->messages);
        $domainMessage = $messageBus->messages[0]->getEvent();

        self::assertInstanceOf(LeadAcquired::class, $domainMessage);
        self::assertSame($contact, $domainMessage->getContact());
        self::assertEquals(1, $domainMessage->getId());
        self::assertEquals('Awesome sponsor', $domainMessage->getName());
    }
}

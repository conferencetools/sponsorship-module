<?php

namespace ConferenceTools\Sponsorship\Domain\Model;

use ConferenceTools\Sponsorship\Domain\Command\Lead\AcquireLead;
use ConferenceTools\Sponsorship\Domain\CommandHandler\Lead as LeadCommandHandler;
use ConferenceTools\Sponsorship\Domain\Event\Lead\LeadAcquired;
use ConferenceTools\Sponsorship\Domain\Model\Lead\Lead;
use ConferenceTools\Sponsorship\Domain\ValueObject\Contact;
use ConferenceTools\Sponsorship\Testing\AbstractBusTest;

class LeadTest extends AbstractBusTest
{
    protected $modelClass = Lead::class;

    public function testLeadAcquired()
    {
        $sut = new LeadCommandHandler($this->repository, $this->idGenerator);
        $this->setupLogger($sut);

        $contact = new Contact('Zoe', 'zoe@awesomesponsor.com', '01233 123456');
        $command = new AcquireLead('Awesome sponsor', $contact);

        $sut->handle($command);

        self::assertCount(1, $this->messageBus->messages);
        $domainMessage = $this->messageBus->messages[0]->getEvent();

        self::assertInstanceOf(LeadAcquired::class, $domainMessage);
        self::assertSame($contact, $domainMessage->getContact());
        self::assertEquals(1, $domainMessage->getId());
        self::assertEquals('Awesome sponsor', $domainMessage->getName());
    }
}

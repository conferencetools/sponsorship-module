<?php

namespace ConferenceTools\Sponsorship\Domain\CommandHandler;

use Carnage\Cqrs\Aggregate\Identity\GeneratorInterface;
use Carnage\Cqrs\MessageHandler\AbstractMethodNameMessageHandler;
use Carnage\Cqrs\Persistence\Repository\RepositoryInterface;
use ConferenceTools\Sponsorship\Domain\Command\Lead\AcquireLead;
use ConferenceTools\Sponsorship\Domain\Model\Lead\Lead as LeadAggregate;

class Lead extends AbstractMethodNameMessageHandler
{
    private $leadRepository;
    private $identityGenerator;

    /**
     * Lead constructor.
     * @param $leadRepository
     * @param $identityGenerator
     */
    public function __construct(RepositoryInterface $leadRepository, GeneratorInterface $identityGenerator)
    {
        $this->leadRepository = $leadRepository;
        $this->identityGenerator = $identityGenerator;
    }

    protected function handleAcquireLead(AcquireLead $command)
    {
        $lead = LeadAggregate::leadAcquired(
            $this->identityGenerator->generateIdentity(),
            $command->getName(),
            $command->getContact()
        );

        $this->leadRepository->save($lead);
    }
}

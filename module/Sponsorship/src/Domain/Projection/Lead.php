<?php

namespace ConferenceTools\Sponsorship\Domain\Projection;

use Carnage\Cqrs\MessageHandler\AbstractMethodNameMessageHandler;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\StartedWithLead;
use ConferenceTools\Sponsorship\Domain\Event\Lead\LeadAcquired;
use ConferenceTools\Sponsorship\Domain\ReadModel\Lead\Lead as LeadEntity;
use ConferenceTools\Sponsorship\Infra\ReadRepo\DoctrineRepository;
use Doctrine\Common\Collections\Criteria;

class Lead extends AbstractMethodNameMessageHandler
{
    private $repository;

    /**
     * @TODO replace with interface.
     */
    public function __construct(DoctrineRepository $repository)
    {
        $this->repository = $repository;
    }

    protected function handleLeadAcquired(LeadAcquired $event)
    {
        $lead = new LeadEntity($event->getId(), $event->getName(), $event->getContact());
        $this->repository->add($lead);
        $this->repository->commit();
    }

    protected function handleStartedWithLead(StartedWithLead $event)
    {
        $lead = $this->loadLead($event->getLeadId());
        $lead->addConversation($event->getId());

        $this->repository->commit();
    }

    /**
     * @param string $id
     * @return LeadEntity
     */
    private function loadLead(string $id): LeadEntity
    {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq('leadId', $id));
        $lead = $this->repository->matching($criteria)->first();

        return $lead;
    }
}
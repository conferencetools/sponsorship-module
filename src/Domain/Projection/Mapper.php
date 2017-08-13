<?php

namespace ConferenceTools\Sponsorship\Domain\Projection;

use Carnage\Cqrs\MessageHandler\AbstractMethodNameMessageHandler;
use Carnage\Cqrs\Persistence\ReadModel\RepositoryInterface;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\AssignedToLead;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\MessageReceived;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\MessageSent;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\StartedWithLead;
use ConferenceTools\Sponsorship\Domain\Event\Lead\LeadAcquired;
use ConferenceTools\Sponsorship\Domain\ReadModel\Mapping\Mapping;
use Doctrine\Common\Collections\Criteria;

class Mapper extends AbstractMethodNameMessageHandler
{
    /**
     * @var RepositoryInterface
     */
    private $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    protected function handleStartedWithLead(StartedWithLead $event)
    {
        $map = Mapping::ConversationToLead($event->getId(), $event->getLeadId());
        $this->repository->add($map);
        $this->repository->commit();
    }

    protected function handleAssignedToLead(AssignedToLead $event)
    {
        $map = Mapping::ConversationToLead($event->getId(), $event->getLeadId());
        $this->repository->add($map);
        $this->repository->commit();
    }

    protected function handleLeadAcquired(LeadAcquired $event)
    {
        $map = Mapping::EmailToLead($event->getId(), $event->getContact()->getEmail());
        $this->repository->add($map);
        $this->repository->commit();
    }

    protected function handleMessageReceived(MessageReceived $event)
    {
        if ($this->alreadyMappedConversation($event->getId(), $event->getFrom()->getEmail())) {
            $map = Mapping::EmailToConversation($event->getId(), $event->getFrom()->getEmail());
            $this->repository->add($map);
            $this->repository->commit();
        }
    }

    protected function handleMessageSent(MessageSent $event)
    {
        $search = Criteria::create();
        $search->where(Criteria::expr()->eq('conversationId', $event->getId()));
        $search->andWhere(Criteria::expr()->neq('leadId', null));
        /** @var Mapping $conversationMap */
        $conversationMap = $this->repository->matching($search)->current();

        $search = Criteria::create();
        $search->where(Criteria::expr()->eq('conversationId', null));
        $search->andWhere(Criteria::expr()->eq('leadId', $conversationMap->getLeadId()));
        /** @var Mapping $leadMap */
        $leadMap = $this->repository->matching($search)->current();

        if ($this->alreadyMappedConversation($event->getId(), $leadMap->getEmail())) {
            $map = Mapping::EmailToConversation($event->getId(), $leadMap->getEmail());
            $this->repository->add($map);
            $this->repository->commit();
        }
    }

    private function alreadyMappedConversation(string $conversationId, string $email): bool
    {
        $criteria = Criteria::create();
        $criteria->where(
            Criteria::expr()->andX(
                Criteria::expr()->eq('conversationId', $conversationId),
                Criteria::expr()->eq('email', $email)
            )
        );

        return !($this->repository->matching($criteria)->count() === 0);
    }
}
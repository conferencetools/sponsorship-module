<?php

namespace ConferenceTools\Sponsorship\Domain\Projection;

use Carnage\Cqrs\MessageHandler\AbstractMethodNameMessageHandler;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\MessageReceived;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\MessageSent;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\StartedWithLead;
use ConferenceTools\Sponsorship\Domain\ReadModel\Conversation\Conversation as ConversationEntity;
use ConferenceTools\Sponsorship\Domain\ReadModel\Conversation\Message;
use ConferenceTools\Sponsorship\Infra\ReadRepo\DoctrineRepository;
use Doctrine\Common\Collections\Criteria;

class Conversation extends AbstractMethodNameMessageHandler
{
    private $repository;

    /**
     * @TODO replace with interface.
     */
    public function __construct(DoctrineRepository $repository)
    {
        $this->repository = $repository;
    }

    protected function handleStartedWithLead(StartedWithLead $event)
    {
        $entity = new ConversationEntity($event->getId());
        $this->repository->add($entity);
        $this->repository->commit();
    }

    protected function handleMessageReceived(MessageReceived $event)
    {
        $conversation = $this->loadConversation($event->getId());
        $conversation->addMessage($event->getMessage(), Message::DIRECTION_INBOUND);
        $this->repository->commit();
    }


    protected function handleMessageSent(MessageSent $event)
    {
        $conversation = $this->loadConversation($event->getId());
        $conversation->addMessage($event->getMessage(), Message::DIRECTION_OUTBOUND);
        $this->repository->commit();
    }

    /**
     * @param string $id
     * @return ConversationEntity
     */
    private function loadConversation(string $id): ConversationEntity
    {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq('conversationId', $id));
        $conversation = $this->repository->matching($criteria)->first();

        return $conversation;
    }
}
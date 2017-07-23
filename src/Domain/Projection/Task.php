<?php

namespace ConferenceTools\Sponsorship\Domain\Projection;

use Carnage\Cqrs\MessageHandler\AbstractMethodNameMessageHandler;
use Carnage\Cqrs\Persistence\ReadModel\RepositoryInterface;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\MessageReceived;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\MessageSent;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\ReplyOutstanding;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\ResponseOutstanding;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\StartedWithLead;
use ConferenceTools\Sponsorship\Domain\Event\Lead\LeadAcquired;
use ConferenceTools\Sponsorship\Domain\ReadModel\Task\Task as TaskEntity;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;

class Task extends AbstractMethodNameMessageHandler
{
    private $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    protected function handleStartedWithLead(StartedWithLead $event)
    {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq('leadId', $event->getLeadId()));
        $criteria->andWhere(Criteria::expr()->in('taskType', [TaskEntity::TYPE_START_A_CONVERSATION]));

        /** @var Collection $tasks */
        $tasks = $this->repository->matching($criteria);

        if ((int) $tasks->count() !== 0) {
            $this->closeTasks(
                $event->getLeadId(),
                [
                    TaskEntity::TYPE_START_A_CONVERSATION,
                ]
            );

            $task = TaskEntity::sendFirstEmail($event->getLeadId(), $event->getId());
            $this->repository->add($task);
            $this->repository->commit();
        }
    }

    protected function handleLeadAcquired(LeadAcquired $event)
    {
        $task = TaskEntity::startAConversation($event->getId());
        $this->repository->add($task);
        $this->repository->commit();
    }

    protected function handleMessageReceived(MessageReceived $event)
    {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq('conversationId', $event->getId()));
        $criteria->andWhere(Criteria::expr()->in('taskType', [TaskEntity::TYPE_REPLY_TO_MESSAGE]));

        /** @var Collection $tasks */
        $tasks = $this->repository->matching($criteria);

        if ($tasks->count() === 0) {
            //setup a task to reply to the message
            $task = TaskEntity::replyToMessage($event->getId());
            $this->repository->add($task);
            //close any task for sending chase emails
            $this->closeTasks($event->getId(), [TaskEntity::TYPE_SEND_FOLLOW_UP]);
            $this->repository->commit();
        }
    }

    protected function handleMessageSent(MessageSent $event)
    {
        //close any task for replying to a message and for sending the first message
        $this->closeTasks(
            $event->getId(),
            [
                TaskEntity::TYPE_SEND_FOLLOW_UP,
                TaskEntity::TYPE_SENT_FIRST_EMAIL,
                TaskEntity::TYPE_REPLY_TO_MESSAGE
            ]
        );
        $this->repository->commit();
    }

    protected function handleReplyOutstanding(ReplyOutstanding $event)
    {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq('conversationId', $event->getId()));

        /** @var TaskEntity $task */
        $task = $this->repository->matching($criteria)->current();
        $task->escalate();
        $this->repository->commit();
    }

    protected function handleResponseOutstanding(ResponseOutstanding $event)
    {
        //setup a task to send chase email
        $task = TaskEntity::sendFollowUp($event->getId());
        $this->repository->add($task);
        $this->repository->commit();
    }

    /**
     * @param MessageSent $event
     */
    protected function closeTasks(string $identifiedBy, array $taskTypes)
    {
        $criteria = Criteria::create();
        $criteria->where(
            Criteria::expr()->orX(
                Criteria::expr()->eq('conversationId', $identifiedBy),
                Criteria::expr()->eq('leadId', $identifiedBy)
            )
        );
        $criteria->andWhere(Criteria::expr()->in('taskType', $taskTypes));

        $tasks = $this->repository->matching($criteria);
        foreach ($tasks as $task) {
            $this->repository->remove($task);
        }
    }
}
<?php

namespace ConferenceTools\Sponsorship\Domain\ProcessManager;

use Carnage\Cqrs\MessageHandler\AbstractMethodNameMessageHandler;
use Carnage\Cqrs\Persistence\EventStore\NotFoundException;
use Carnage\Cqrs\Persistence\Repository\RepositoryInterface;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\MessageReceived;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\ReplyTimeout;
use ConferenceTools\Sponsorship\Domain\Process\Conversation as ConversationProcess;

class Conversation extends AbstractMethodNameMessageHandler
{
    private $repository;

    /**
     * Conversation constructor.
     * @param $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    protected function handleMessageReceived(MessageReceived $event)
    {
        $process = $this->loadProcess($event->getId());
        $process->messageReceived();
        $process->apply($event);
        $this->repository->save($process);
    }

    protected function handleReplyTimeout(ReplyTimeout $event)
    {
        $process = $this->loadProcess($event->getConversationId());
        $process->replyTimeout();
        $process->apply($event);
        $this->repository->save($process);
    }

    private function loadProcess($id): ConversationProcess
    {
        try {
            $process = $this->repository->load($id);
        } catch (NotFoundException $e) {
            $process = ConversationProcess::withId($id);
        }

        return $process;
    }
}
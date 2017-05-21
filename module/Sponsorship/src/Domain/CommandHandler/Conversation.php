<?php

namespace ConferenceTools\Sponsorship\Domain\CommandHandler;

use Carnage\Cqrs\Aggregate\Identity\GeneratorInterface;
use Carnage\Cqrs\MessageHandler\AbstractMethodNameMessageHandler;
use Carnage\Cqrs\Persistence\Repository\RepositoryInterface;
use ConferenceTools\Sponsorship\Domain\Command\Conversation\RecordMessage;
use ConferenceTools\Sponsorship\Domain\Command\Conversation\StartWithLead;
use ConferenceTools\Sponsorship\Domain\Model\Conversation\Conversation as ConversationAggregate;

class Conversation extends AbstractMethodNameMessageHandler
{
    private $conversationRepository;
    private $identityGenerator;

    public function __construct(RepositoryInterface $conversationRepository, GeneratorInterface $identityGenerator)
    {
        $this->conversationRepository = $conversationRepository;
        $this->identityGenerator = $identityGenerator;
    }

    protected function handleStartWithLead(StartWithLead $command)
    {
        $conversation = ConversationAggregate::fromNewLead(
            $this->identityGenerator->generateIdentity(),
            $command->getLeadId()
        );

        $this->conversationRepository->save($conversation);
    }

    protected function handleRecordMessage(RecordMessage $command)
    {
        $conversation = $this->loadConversation($command->getConversationId());
        $conversation->messageReceived($command->getFrom(), $command->getMessage());
        $this->conversationRepository->save($conversation);
    }

    protected function loadConversation(string $conversationId): ConversationAggregate
    {
        return $conversation = $this->conversationRepository->load($conversationId);
    }
}

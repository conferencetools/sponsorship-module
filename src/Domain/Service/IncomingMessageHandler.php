<?php

namespace ConferenceTools\Sponsorship\Domain\Service;

use Carnage\Cqrs\MessageBus\MessageBusInterface;
use Carnage\Cqrs\Persistence\ReadModel\RepositoryInterface;
use ConferenceTools\Sponsorship\Domain\Command\Conversation\RecordMessage;
use ConferenceTools\Sponsorship\Domain\Command\Conversation\StartWithMessage;
use ConferenceTools\Sponsorship\Domain\ReadModel\Conversation\Conversation;
use ConferenceTools\Sponsorship\Domain\ValueObject\Contact;
use ConferenceTools\Sponsorship\Domain\ValueObject\Message;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;

final class IncomingMessageHandler
{
    /**
     * @var MessageBusInterface
     */
    private $commandBus;
    /**
     * @var RepositoryInterface
     */
    private $conversationRepository;

    /**
     * IncomingMessageHandler constructor.
     * @param $commandBus
     */
    public function __construct(MessageBusInterface $commandBus, RepositoryInterface $conversationRepository)
    {
        $this->commandBus = $commandBus;
        $this->conversationRepository = $conversationRepository;
    }

    public function handleIncomingMessage(Message $message, Contact $contact)
    {
        $search = Criteria::create();
        /** @var Collection $results */
        $results = $this->conversationRepository->matching($search);

        if ($results->isEmpty()) {
            $command = new StartWithMessage($contact, $message);
            $this->commandBus->dispatch($command);
        } else {
            /** @var Conversation $conversation */
            $conversation = $results->first();
            //@TODO what if we have more than one result?
            $command = new RecordMessage(
                $conversation->getConversationId(),
                //@TODO either grab contact from conversation or build from message
                $contact,
                $message
            );

            $this->commandBus->dispatch($command);
        }
    }
}
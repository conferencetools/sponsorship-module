<?php

namespace ConferenceTools\Sponsorship\Domain\Service;

use Carnage\Cqrs\MessageBus\MessageBusInterface;
use Carnage\Cqrs\Persistence\ReadModel\RepositoryInterface;
use ConferenceTools\Sponsorship\Domain\Command\Conversation\RecordMessage;
use ConferenceTools\Sponsorship\Domain\Command\Conversation\StartWithMessage;
use ConferenceTools\Sponsorship\Domain\ReadModel\Conversation\Conversation;
use ConferenceTools\Sponsorship\Domain\ReadModel\Mapping\Mapping;
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
    private $mappingRepository;

    /**
     * IncomingMessageHandler constructor.
     * @param $commandBus
     */
    public function __construct(MessageBusInterface $commandBus, RepositoryInterface $mappingRepository)
    {
        $this->commandBus = $commandBus;
        $this->mappingRepository = $mappingRepository;
    }

    public function handleIncomingMessage(Message $message, Contact $contact)
    {
        $search = Criteria::create();
        $search->where(Criteria::expr()->eq('email', $contact->getEmail()));
        $search->andWhere(Criteria::expr()->isNull('leadId'));
        /** @var Collection $results */
        $results = $this->mappingRepository->matching($search);

        if ($results->isEmpty()) {
            //@TODO handle case of if we can find a match for a lead we've not contacted yet
            $command = new StartWithMessage($contact, $message);
            $this->commandBus->dispatch($command);
        } else {
            /** @var Mapping $conversation */
            $conversation = $results->first();
            //@TODO what if we have more than one result?
            $command = new RecordMessage(
                $conversation->getConversationId(),
                $contact,
                $message
            );

            $this->commandBus->dispatch($command);
        }
    }
}
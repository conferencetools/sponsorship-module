<?php

namespace ConferenceTools\Sponsorship\Cli\Command;

use Carnage\Cqrs\MessageBus\MessageBusInterface;
use ConferenceTools\Sponsorship\Domain\Command\Conversation\RecordMessage;
use ConferenceTools\Sponsorship\Domain\ReadModel\Conversation\Conversation;
use ConferenceTools\Sponsorship\Domain\ValueObject\Contact;
use ConferenceTools\Sponsorship\Domain\ValueObject\Message;
use ConferenceTools\Sponsorship\Infra\ReadRepo\DoctrineRepository;
use ConferenceTools\Sponsorship\Service\Mailgun\Client;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RetrieveEvents extends Command
{
    /**
     * @var MessageBusInterface
     */
    private $commandBus;

    /**
     * @var DoctrineRepository
     */
    private $conversationRepository;

    /**
     * @var Client
     */
    private $mailgun;

    public static function build(
        MessageBusInterface $commandBus,
        DoctrineRepository $conversationRepository,
        Client $mailgun
    ) {
        $instance = new static();
        $instance->commandBus = $commandBus;
        $instance->conversationRepository = $conversationRepository;
        $instance->mailgun = $mailgun;

        return $instance;
    }

    protected function configure()
    {
        $this->setName('conferencetools:sponsorship:retrieve-messages')
            ->setDescription('Retrieves messages stored in mail box');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $messages = $this->mailgun->fetchEmailMessages();

        foreach ($messages as $emailPayload) {
            //@TODO the whole of this probably needs moving into either a process manager or a domain service
            $message = new Message($emailPayload['subject'], $emailPayload['stripped-text']);

            //@TODO add criteria to serach by email address of sender
            $search = Criteria::create();
            $results = $this->conversationRepository->matching($search);

            if ($results->isEmpty()) {
                //@TODO handle case where we don't have a lead matching this email eg create a new lead
            } else {
                /** @var Conversation $conversation */
                $conversation = $results->first();
                $command = new RecordMessage(
                    $conversation->getConversationId(),
                    //@TODO either grab contact from conversation or build from message
                    new Contact('unknown', 'unknown@unknown.com'),
                    $message
                );

                $this->commandBus->dispatch($command);
            }
        }
    }
}

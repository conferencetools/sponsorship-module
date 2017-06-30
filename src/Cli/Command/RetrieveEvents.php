<?php

namespace ConferenceTools\Sponsorship\Cli\Command;

use Carnage\Cqrs\MessageBus\MessageBusInterface;
use Carnage\Cqrs\Persistence\ReadModel\RepositoryInterface;
use ConferenceTools\Sponsorship\Domain\Command\Conversation\RecordMessage;
use ConferenceTools\Sponsorship\Domain\ReadModel\Conversation\Conversation;
use ConferenceTools\Sponsorship\Domain\Service\IncomingMessageHandler;
use ConferenceTools\Sponsorship\Domain\ValueObject\Contact;
use ConferenceTools\Sponsorship\Domain\ValueObject\Message;
use ConferenceTools\Sponsorship\Service\Mailgun\Client;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RetrieveEvents extends Command
{
    /**
     * @var Client
     */
    private $mailgun;

    /**
     * @var IncomingMessageHandler
     */
    private $messageHandler;

    public static function build(
        IncomingMessageHandler $messageHandler,
        Client $mailgun
    ) {
        $instance = new static();
        $instance->messageHandler = $messageHandler;
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
            //@TODO either grab contact from conversation or build from message
            $from = new Contact('unknown', 'unknown@unknown.com');
            $this->messageHandler->handleIncomingMessage($message, $from);
        }
    }
}

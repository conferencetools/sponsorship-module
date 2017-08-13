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
use Zend\Mail\AddressList;

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
            //@TODO handle issues if there isn't a strippedtext field (use stripped html?)
            //@TODO what if their contents differ?
            $message = new Message($emailPayload['subject'], $emailPayload['stripped-text']);
            //@TODO either grab contact from conversation or build from message

            $addressList = new AddressList();
            //the from field seems to be the most reliable for determining the sender
            //(consider forwarded messages) Return-Path is an alternative if there are issues around this
            $addressList->addFromString($emailPayload['from']);
            $emailFrom = $addressList->current();

            $from = new Contact($emailFrom->getName(), $emailFrom->getEmail());
            $this->messageHandler->handleIncomingMessage($message, $from);
        }
    }
}

<?php

namespace ConferenceTools\Sponsorship\EventListener;

use Carnage\Cqrs\Event\DomainMessage;
use Carnage\Cqrs\MessageBus\MessageInterface;
use Carnage\Cqrs\MessageHandler\MessageHandlerInterface;
use Carnage\Cqrs\Persistence\ReadModel\RepositoryInterface;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\MessageSent;
use ConferenceTools\Sponsorship\Domain\ReadModel\Mapping\Mapping;
use Doctrine\Common\Collections\Criteria;
use Zend\Http\Response;
use Zend\Mail\Message;
use Zend\Mail\Transport\TransportInterface;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;
use Zend\View\Model\ViewModel;
use Zend\View\View;

class SendMail implements MessageHandlerInterface
{
    /**
     * @var View
     */
    private $view;
    /**
     * @var TransportInterface
     */
    private $mail;
    /**
     * @var array
     */
    private $config;
    /**
     * @var RepositoryInterface
     */
    private $mappingRespository;

    /**
     * EmailPurchase constructor.
     * @param View $view
     * @param TransportInterface $mail
     * @param array $config
     */
    public function __construct(RepositoryInterface $mappingRespository, View $view, TransportInterface $mail, array $config = [])
    {
        $this->view = $view;
        $this->mail = $mail;
        $this->config = $config;
        $this->mappingRespository = $mappingRespository;
    }

    public function handleDomainMessage(DomainMessage $message)
    {
        $this->handle($message->getEvent());
    }

    public function handle(MessageInterface $message)
    {
        if (!($message instanceof MessageSent)) {
            return;
        }

        $email = $message->getMessage();
        $viewModel = new ViewModel(['body' => $email->getBody()]);
        $viewModel->setTerminal(true);
        $viewModel->setTemplate('email/outbound');

        $response = new Response();
        $this->view->setResponse($response);
        $this->view->render($viewModel);
        $html = $response->getContent();

        $emailMessage = $this->buildMessage($email->getSubject(), $html);
        $emailMessage->setTo($this->fetchEmailAddress($message->getId()));

        $this->mail->send($emailMessage);
    }

    private function buildMessage($subject, $htmlMarkup)
    {
        $html = new MimePart($htmlMarkup);
        $html->setCharset('UTF-8');
        $html->type = "text/html";

        $body = new MimeMessage();
        $body->setParts(array($html));

        $message = new Message();
        $message->setBody($body);
        $message->setSubject($subject);
        if (isset($this->config['from'])) {
            $message->setFrom($this->config['from']);
        }
        $message->setEncoding('UTF-8');

        return $message;
    }

    private function fetchEmailAddress(string $conversationId): string
    {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq('conversationId', $conversationId));
        $criteria->andWhere(Criteria::expr()->isNull('leadId'));

        /** @var Mapping[] $emailAddresses */
        $emailAddresses = $this->mappingRespository->matching($criteria);

        foreach ($emailAddresses as $emailAddress) {
            if ($emailAddress->getEmail() !== null) {
                return $emailAddress->getEmail();
            }
        }
    }
}

<?php

namespace ConferenceTools\Sponsorship\Controller;

use BsbFlysystem\Service\FilesystemManager;
use Carnage\Cqorms\Persistence\ReadModel\DoctrineRepository;
use Carnage\Cqrs\MessageBus\MessageBusInterface;
use ConferenceTools\Sponsorship\Domain\Command\Conversation\AssignToLead;
use ConferenceTools\Sponsorship\Domain\Command\Conversation\SendMessage;
use ConferenceTools\Sponsorship\Domain\Command\Conversation\StartWithLead;
use ConferenceTools\Sponsorship\Domain\Command\Lead\AcquireLead;
use ConferenceTools\Sponsorship\Domain\Event\Conversation\StartedWithLead;
use ConferenceTools\Sponsorship\Domain\Event\Lead\LeadAcquired;
use ConferenceTools\Sponsorship\Domain\ReadModel\Conversation\Conversation;
use ConferenceTools\Sponsorship\Domain\ValueObject\Contact;
use ConferenceTools\Sponsorship\Domain\ValueObject\File as FileObject;
use ConferenceTools\Sponsorship\Domain\ValueObject\Message;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use League\Flysystem\FilesystemInterface;
use Zend\Form\Element\File;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Text;
use Zend\Form\Element\Textarea;
use Zend\Form\Form;
use Zend\Http\Request;
use Zend\Stdlib\ArrayUtils;
use Zend\View\Model\ViewModel;

class ConversationController extends AbstractController
{
    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    public function __construct(
        MessageBusInterface $commandBus,
        EntityManager $entityManager,
        FilesystemInterface $filesystem
    ) {
        parent::__construct($commandBus, $entityManager);
        $this->filesystem = $filesystem;
    }

    public function startAction()
    {
        $leadId = $this->params()->fromRoute('leadId');

        $command = new StartWithLead($leadId);

        $this->getCommandBus()->dispatch($command);

        /** @var StartedWithLead $event */
        $event = current($this->events()->getEventsByType(StartedWithLead::class));
        $conversationId = $event->getId();

        return $this->redirect()->toRoute('sponsorship/conversation/reply', ['conversationId' => $conversationId]);
    }

    public function replyAction()
    {
        $conversationId = $this->params()->fromRoute('conversationId');
        $conversation = $this->loadConversation($conversationId);

        if ($conversation->hasLead()) {
            $form = $this->createMessageForm();
            /** @var Request $request */
            $request = $this->getRequest();
            if ($request->isPost()) {
                $form->setData(ArrayUtils::merge($request->getPost()->toArray(), $request->getFiles()->toArray()));
                if ($form->isValid()) {
                    $data = $form->getData();
                    $files = $this->handleFiles($data);

                    $message = new Message($data['subject'], $data['body'], ...$files);
                    $command = new SendMessage($conversationId, $message);

                    $this->getCommandBus()->dispatch($command);
                }
            }
        } else {
            $form = $this->createNewLeadForm();
            $contact = $conversation->getPrimaryContact();
            $data = [
                'contact_name' => $contact->getName(),
                'contact_email' => $contact->getEmail()
            ];
            $form->setData($data);

            /** @var Request $request */
            $request = $this->getRequest();
            if ($request->isPost()) {
                $form->setData($request->getPost()->toArray());
                if ($form->isValid()) {
                    $data = $form->getData();
                    $contact = new Contact($data['contact_name'], $data['contact_email']);
                    $command = new AcquireLead($data['company_name'], $contact);

                    $this->getCommandBus()->dispatch($command);

                    $events = $this->events()->getEventsByType(LeadAcquired::class);
                    /** @var LeadAcquired $leadEvent */
                    $leadEvent = current($events);

                    $command = new AssignToLead($conversationId, $leadEvent->getId());

                    $this->getCommandBus()->dispatch($command);
                    $form = $this->createMessageForm();
                }
            }
        }



        return new ViewModel(['form' => $form, 'conversation' => $conversation]);
    }

    /**
     * @return Form
     */
    private function createMessageForm(): Form
    {
        $form = new Form();
        $form->add(new Text('subject', ['label' => 'Subject']));
        $form->add(new Textarea('body', ['label' => 'Body']));
        $form->add(new File('attachment', ['label' => 'Attachment']));
        $form->add(new Submit('submit', ['label' => 'Send']));
        return $form;
    }

    private function createNewLeadForm(): Form
    {
        //@TODO abstract this -> in use in two places
        $form = new Form();
        $form->add(new Text('company_name', ['label' => 'Company name']));
        $form->add(new Text('contact_name', ['label' => 'Contact name']));
        $form->add(new Text('contact_email', ['label' => 'Company email']));
        $form->add(new Submit('submit', ['label' => 'Save']));

        return $form;
    }

    /**
     * @param $data
     * @return array
     */
    private function handleFiles($data): array
    {
        $files = [];

        if (isset($data['attachment']['tmp_name']) && is_file($data['attachment']['tmp_name'])) {
            //@TODO use identity generator here to ensure a better filename
            $destination = 'files/' . uniqid();
            $this->filesystem->writeStream($destination, fopen($data['attachment']['tmp_name'], 'r+'));
            $files[] = new FileObject($data['attachment']['name'], $destination);
        }

        return $files;
    }

    /**
     * @param $conversationId
     * @return Conversation
     */
    private function loadConversation($conversationId): Conversation
    {
        $em = $this->getServiceLocator()->get(EntityManager::class);
        $repo = new DoctrineRepository(Conversation::class, $em);
        $search = Criteria::create();
        $search->where(Criteria::expr()->eq('conversationId', $conversationId));
        $conversation = $repo->matching($search)->first();
        return $conversation;
    }
}
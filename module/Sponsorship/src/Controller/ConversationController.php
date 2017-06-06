<?php

namespace ConferenceTools\Sponsorship\Controller;

use ConferenceTools\Sponsorship\Domain\Command\Conversation\SendMessage;
use ConferenceTools\Sponsorship\Domain\Command\Lead\AcquireLead;
use ConferenceTools\Sponsorship\Domain\ReadModel\Conversation\Conversation;
use ConferenceTools\Sponsorship\Domain\ValueObject\Contact;
use ConferenceTools\Sponsorship\Domain\ValueObject\Message;
use ConferenceTools\Sponsorship\Infra\ReadRepo\DoctrineRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Text;
use Zend\Form\Element\Textarea;
use Zend\Form\Form;
use Zend\Http\Request;
use Zend\View\Model\ViewModel;

class ConversationController extends AbstractController
{
    public function replyAction()
    {
        $form = new Form();
        $form->add(new Text('subject', ['label' => 'Subject']));
        $form->add(new Textarea('body', ['label' => 'Body']));
        $form->add(new Submit('submit', ['label' => 'Send']));

        $conversationId = $this->params()->fromRoute('conversationId');

        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $message = new Message($data['subject'], $data['body']);
                $command = new SendMessage($conversationId, $message);

                $this->getCommandBus()->dispatch($command);
                $this->redirect()->toRoute('root');
            }
        }

        $em = $this->getServiceLocator()->get(EntityManager::class);
        $repo = new DoctrineRepository(Conversation::class, $em);
        $search = Criteria::create();
        $search->where(Criteria::expr()->eq('conversationId', $conversationId));
        $conversation = $repo->matching($search)->first();

        return new ViewModel(['form' => $form, 'conversation' => $conversation]);
    }
}
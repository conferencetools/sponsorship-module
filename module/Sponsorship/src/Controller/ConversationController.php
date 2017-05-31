<?php

namespace ConferenceTools\Sponsorship\Controller;

use ConferenceTools\Sponsorship\Domain\Command\Conversation\SendMessage;
use ConferenceTools\Sponsorship\Domain\Command\Lead\AcquireLead;
use ConferenceTools\Sponsorship\Domain\ValueObject\Contact;
use ConferenceTools\Sponsorship\Domain\ValueObject\Message;
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
        $form->add(new Text('subject'));
        $form->add(new Textarea('body'));
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

        return new ViewModel(['form' => $form]);
    }
}
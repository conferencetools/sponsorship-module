<?php

namespace ConferenceTools\Sponsorship\Controller;

use ConferenceTools\Sponsorship\Domain\Command\Lead\AcquireLead;
use ConferenceTools\Sponsorship\Domain\ValueObject\Contact;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\Http\Request;
use Zend\View\Model\ViewModel;

class LeadController extends AbstractController
{
    public function newLeadAction()
    {
        $form = new Form();
        $form->add(new Text('company_name'));
        $form->add(new Text('contact_name'));
        $form->add(new Text('contact_email'));
        $form->add(new Submit('submit', ['label' => 'Save']));

        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $contact = new Contact($data['contact_name'], $data['contact_email']);
                $command = new AcquireLead($data['company_name'], $contact);

                $this->getCommandBus()->dispatch($command);
                $this->redirect()->toRoute('root');
            }
        }

        return new ViewModel(['form' => $form]);
    }
}
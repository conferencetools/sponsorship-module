<?php

namespace ConferenceTools\Sponsorship\Controller;

use Carnage\Cqorms\Persistence\ReadModel\DoctrineRepository;
use ConferenceTools\Sponsorship\Domain\Command\Lead\AcquireLead;
use ConferenceTools\Sponsorship\Domain\ReadModel\Lead\Lead;
use ConferenceTools\Sponsorship\Domain\ValueObject\Contact;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\Http\Request;
use Zend\View\Model\ViewModel;

class LeadController extends AbstractController
{
    public function indexAction()
    {
        $em = $this->getServiceLocator()->get(EntityManager::class);
        $repo = new DoctrineRepository(Lead::class, $em);
        $leads = $repo->matching(Criteria::create());
        return new ViewModel(['leads' => $leads]);
    }

    public function viewLeadAction()
    {
        $leadId = $this->params()->fromRoute('leadId');

        $em = $this->getServiceLocator()->get(EntityManager::class);
        $repo = new DoctrineRepository(Lead::class, $em);
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq('leadId', $leadId));
        $leads = $repo->matching($criteria);

        return new ViewModel(['lead' => $leads->first()]);
    }

    public function newLeadAction()
    {
        $form = new Form();
        $form->add(new Text('company_name', ['label' => 'Company name']));
        $form->add(new Text('contact_name', ['label' => 'Contact name']));
        $form->add(new Text('contact_email', ['label' => 'Company email']));
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
                $this->redirect()->toRoute('sponsorship');
            }
        }

        return new ViewModel(['form' => $form]);
    }
}
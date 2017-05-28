<?php

namespace ConferenceTools\Sponsorship\Controller;

use ConferenceTools\Sponsorship\Domain\ReadModel\Task\Task;
use Zend\View\Model\ViewModel;

class TaskController extends AbstractController
{
    public function indexAction()
    {
        $repository = $this->getEntityManager()->getRepository(Task::class);
        $tasks = $repository->findBy([], ['priority' => 'DESC']);

        return new ViewModel(['tasks' => $tasks]);
    }
}

<?php

namespace ConferenceTools\Sponsorship\Domain\CommandHandler;

use Carnage\Cqrs\MessageHandler\AbstractMethodNameMessageHandler;
use Carnage\Cqrs\Persistence\EventStore\NotFoundException;
use Carnage\Cqrs\Persistence\Repository\RepositoryInterface;
use ConferenceTools\Sponsorship\Domain\Command\AlarmClock\SendAt;
use ConferenceTools\Sponsorship\Domain\Command\AlarmClock\WakeUp;
use ConferenceTools\Sponsorship\Domain\Model\AlarmClock as AlarmClockModel;

class AlarmClock extends AbstractMethodNameMessageHandler
{
    private $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    protected function handleSendAt(SendAt $command)
    {
        $alarmClock = $this->loadAlarmClock(AlarmClockModel::makeId($command->getWhen()));
        $alarmClock->sendAt($command);
        $this->repository->save($alarmClock);
    }

    protected function handleWakeUp(WakeUp $command)
    {
        $alarmClock = $this->loadAlarmClock(AlarmClockModel::makeId(new \DateTime()));
        $alarmClock->wakeUp();
        $this->repository->save($alarmClock);
    }

    private function loadAlarmClock($id): AlarmClockModel
    {
        try {
            $process = $this->repository->load($id);
        } catch (NotFoundException $e) {
            $process = new AlarmClockModel();
        }

        return $process;
    }
}

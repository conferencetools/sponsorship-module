<?php

namespace ConferenceTools\Sponsorship\Domain\ReadModel\Task;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Task
{
    const TYPE_SENT_FIRST_EMAIL = 'send-first-email';
    const TYPE_REPLY_TO_MESSAGE = 'reply-to-message';
    const TYPE_SEND_FOLLOW_UP = 'send-follow-up';

    /**
     * @var integer
     * @ORM\Id
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", nullable="true")
     */
    private $conversationId;

    /**
     * @var string
     * @ORM\Column(type="string", nullable="true")
     */
    private $leadId;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $taskType;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $priority= 0;

    private function __construct()
    {
    }

    public static function sendFirstEmail($leadId, $conversationId)
    {
        $instance = new self();
        $instance->leadId = $leadId;
        $instance->conversationId = $conversationId;
        $instance->taskType = self::TYPE_SENT_FIRST_EMAIL;

        return $instance;
    }

    public static function replyToMessage($conversationId)
    {
        $instance = new self();
        $instance->conversationId = $conversationId;
        $instance->taskType = self::TYPE_REPLY_TO_MESSAGE;

        return $instance;
    }

    public static function sendFollowUp($conversationId)
    {
        $instance = new self();
        $instance->conversationId = $conversationId;
        $instance->taskType = self::TYPE_SEND_FOLLOW_UP;

        return $instance;
    }

    public function escalate()
    {
        $this->priority++;
    }
}

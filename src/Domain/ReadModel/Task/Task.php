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
    const TYPE_START_A_CONVERSATION = 'start-a-conversation';

    /**
     * @var integer
     * @ORM\Id
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $conversationId;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
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

    public static function startAConversation($leadId)
    {
        $instance = new self();
        $instance->leadId = $leadId;
        $instance->taskType = self::TYPE_START_A_CONVERSATION;

        return $instance;
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

    public function getDescription()
    {
        switch ($this->taskType) {
            case self::TYPE_SENT_FIRST_EMAIL:
                return 'Send first email';

            case self::TYPE_REPLY_TO_MESSAGE:
                return 'Reply to email';

            case self::TYPE_SEND_FOLLOW_UP:
                return 'No response yet, follow up';

            case self::TYPE_START_A_CONVERSATION:
                return 'Start a conversation';
        }
    }

    /**
     * @return string
     */
    public function getConversationId(): ?string
    {
        return $this->conversationId;
    }

    /**
     * @return string
     */
    public function getLeadId(): ?string
    {
        return $this->leadId;
    }

    /**
     * @return string
     */
    public function getTaskType(): string
    {
        return $this->taskType;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }
}

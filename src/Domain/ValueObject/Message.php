<?php

namespace ConferenceTools\Sponsorship\Domain\ValueObject;

use JMS\Serializer\Annotation as JMS;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Message
 * @package ConferenceTools\Sponsorship\Domain\ValueObject
 * @ORM\Embeddable()
 */
class Message
{
    /**
     * @JMS\Type("string")
     * @ORM\Column(type="string")
     * @var string
     */
    private $subject;

    /**
     * @JMS\Type("string")
     * @ORM\Column(type="text")
     * @var string
     */
    private $body;

    /**
     * @ORM\Column(type="json_object", nullable=true)
     * @JMS\Type("ConferenceTools\Sponsorship\Domain\ValueObject\File")
     * @var File[]
     */
    private $attachments;

    public function __construct(string $subject, string $body, File ...$attachments)
    {
        $this->subject = $subject;
        $this->body = $body;
        $this->attachments = empty($attachments) ? null : $attachments;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }
}

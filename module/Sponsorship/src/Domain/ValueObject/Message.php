<?php

namespace ConferenceTools\Sponsorship\Domain\ValueObject;

use JMS\Serializer\Annotation as JMS;

class Message
{
    /**
     * @JMS\Type("string")
     * @var string
     */
    private $subject;

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $body;

    public function __construct(string $subject, string $body)
    {
        $this->subject = $subject;
        $this->body = $body;
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

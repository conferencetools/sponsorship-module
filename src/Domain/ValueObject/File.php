<?php

namespace ConferenceTools\Sponsorship\Domain\ValueObject;

use JMS\Serializer\Annotation as JMS;
use Doctrine\ORM\Mapping as ORM;

final class File
{
    /**
     * @JMS\Type("string")
     * @ORM\Column(type="string")
     * @var string
     */
    private $filename;

    /**
     * @JMS\Type("string")
     * @ORM\Column(type="string")
     * @var string
     */
    private $uri;

    /**
     * File constructor.
     * @param $filename
     * @param $uri
     */
    public function __construct(string $filename, string $uri)
    {
        $this->filename = $filename;
        $this->uri = $uri;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }
}

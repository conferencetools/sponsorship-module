<?php

namespace ConferenceTools\Sponsorship\Domain\ValueObject;

use JMS\Serializer\Annotation as JMS;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable()
 */
class Contact
{
    /**
     * @JMS\Type("string")
     * @ORM\Column(type="string")
     * @var string
     */
    private $name;

    /**
     * @var string
     * @JMS\Type("string")
     * @ORM\Column(type="string")
     */
    private $email;

    /**
     * Contact constructor.
     * @param $name
     * @param $email
     * @param $phoneNumber
     */
    public function __construct(string $name, string $email)
    {
        $this->name = $name;
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }
}

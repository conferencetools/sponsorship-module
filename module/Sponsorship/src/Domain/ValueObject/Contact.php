<?php

namespace ConferenceTools\Sponsorship\Domain\ValueObject;

class Contact
{
    private $name;
    private $email;

    /**
     * Contact constructor.
     * @param $name
     * @param $email
     * @param $phoneNumber
     */
    public function __construct($name, $email)
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

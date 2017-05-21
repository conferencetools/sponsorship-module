<?php

namespace ConferenceTools\Sponsorship\Domain\ValueObject;

class Contact
{
    private $name;
    private $email;
    private $phoneNumber;

    /**
     * Contact constructor.
     * @param $name
     * @param $email
     * @param $phoneNumber
     */
    public function __construct($name, $email, $phoneNumber)
    {
        $this->name = $name;
        $this->email = $email;
        $this->phoneNumber = $phoneNumber;
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

    /**
     * @return mixed
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }
}

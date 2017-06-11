<?php

namespace ConferenceTools\Sponsorship\Domain\ValueObject;

use PHPUnit\Framework\TestCase;

class ContactTest extends TestCase
{
    public function testCreate()
    {
        $sut = new Contact('James', 'james@sponsor.com', '01223 445566');

        self::assertEquals('James', $sut->getName());
        self::assertEquals('james@sponsor.com', $sut->getEmail());
    }
}

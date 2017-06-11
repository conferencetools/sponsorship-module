<?php

namespace ConferenceTools\Sponsorship\Domain\ValueObject;

use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    public function testCreate()
    {
        $sut = new Message('Subject', 'Body');

        self::assertEquals('Subject', $sut->getSubject());
        self::assertEquals('Body', $sut->getBody());
    }
}
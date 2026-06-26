<?php

declare(strict_types=1);

namespace App\Tests\Modules\Invoices\Domain\ValueObjects;

use App\Modules\Invoices\Domain\ValueObjects\CustomerEmail;
use App\Modules\Shared\Exceptions\DomainLogicException;
use PHPUnit\Framework\TestCase;

final class CustomerEmailTest extends TestCase
{
    public function testValidEmailIsAccepted(): void
    {
        $email = new CustomerEmail('user@example.com');

        self::assertSame('user@example.com', $email->email);
    }

    public function testEmptyEmailThrows(): void
    {
        $this->expectException(DomainLogicException::class);

        new CustomerEmail('');
    }

    public function testWhitespaceOnlyEmailThrows(): void
    {
        $this->expectException(DomainLogicException::class);

        new CustomerEmail('   ');
    }

    public function testMalformedEmailThrows(): void
    {
        $this->expectException(DomainLogicException::class);

        new CustomerEmail('not-an-email');
    }

    public function testEmailPreservesOriginalCase(): void
    {
        // Validation normalises for checking, but stored value is unchanged.
        $email = new CustomerEmail('User@Example.COM');

        self::assertSame('User@Example.COM', $email->email);
    }

    public function testEqualsReturnsTrueForIdenticalEmail(): void
    {
        $a = new CustomerEmail('user@example.com');
        $b = new CustomerEmail('user@example.com');

        self::assertTrue($a->equals($b));
    }

    public function testEqualsReturnsTrueForDifferentCase(): void
    {
        $a = new CustomerEmail('User@Example.COM');
        $b = new CustomerEmail('user@example.com');

        self::assertTrue($a->equals($b));
    }

    public function testEqualsReturnsFalseForDifferentEmail(): void
    {
        $a = new CustomerEmail('alice@example.com');
        $b = new CustomerEmail('bob@example.com');

        self::assertFalse($a->equals($b));
    }
}

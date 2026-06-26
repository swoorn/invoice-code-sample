<?php

declare(strict_types=1);

namespace App\Tests\Modules\Invoices\Domain\ValueObjects;

use App\Modules\Invoices\Domain\ValueObjects\CustomerName;
use App\Modules\Shared\Exceptions\DomainLogicException;
use PHPUnit\Framework\TestCase;

final class CustomerNameTest extends TestCase
{
    public function testValidNameIsAccepted(): void
    {
        $name = new CustomerName('Acme Corp');

        self::assertSame('Acme Corp', $name->name);
    }

    public function testEmptyNameThrows(): void
    {
        $this->expectException(DomainLogicException::class);

        new CustomerName('');
    }

    public function testWhitespaceOnlyNameThrows(): void
    {
        $this->expectException(DomainLogicException::class);

        new CustomerName('   ');
    }

    public function testSingleCharacterNameThrows(): void
    {
        $this->expectException(DomainLogicException::class);

        new CustomerName('A');
    }

    public function testNameExceedingMaxLengthThrows(): void
    {
        $this->expectException(DomainLogicException::class);

        new CustomerName(str_repeat('A', 256));
    }

    public function testMinimumLengthIsAccepted(): void
    {
        $name = new CustomerName('Ab');

        self::assertSame('Ab', $name->name);
    }

    public function testMaximumLengthIsAccepted(): void
    {
        $value = str_repeat('A', 255);
        $name = new CustomerName($value);

        self::assertSame($value, $name->name);
    }

    public function testEqualsReturnsTrueForSameName(): void
    {
        $a = new CustomerName('Acme Corp');
        $b = new CustomerName('Acme Corp');

        self::assertTrue($a->equals($b));
    }

    public function testEqualsReturnsFalseForDifferentName(): void
    {
        $a = new CustomerName('Acme Corp');
        $b = new CustomerName('Globex');

        self::assertFalse($a->equals($b));
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Modules\Invoices\Domain\ValueObjects;

use App\Modules\Invoices\Domain\ValueObjects\ProductName;
use App\Modules\Shared\Exceptions\DomainLogicException;
use PHPUnit\Framework\TestCase;

final class ProductNameTest extends TestCase
{
    public function testValidNameIsAccepted(): void
    {
        $name = new ProductName('Widget');

        self::assertSame('Widget', $name->value);
    }

    public function testEmptyNameThrows(): void
    {
        $this->expectException(DomainLogicException::class);

        new ProductName('');
    }

    public function testWhitespaceOnlyNameThrows(): void
    {
        $this->expectException(DomainLogicException::class);

        new ProductName('   ');
    }
}

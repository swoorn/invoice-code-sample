<?php

declare(strict_types=1);

namespace App\Tests\Modules\Invoices\Domain\ValueObjects;

use App\Modules\Invoices\Domain\ValueObjects\Quantity;
use App\Modules\Shared\Exceptions\DomainLogicException;
use PHPUnit\Framework\TestCase;

final class QuantityTest extends TestCase
{
    public function testPositiveQuantityIsAccepted(): void
    {
        $quantity = new Quantity(5);

        self::assertSame(5, $quantity->value);
    }

    public function testZeroQuantityThrows(): void
    {
        $this->expectException(DomainLogicException::class);

        new Quantity(0);
    }

    public function testNegativeQuantityThrows(): void
    {
        $this->expectException(DomainLogicException::class);

        new Quantity(-1);
    }
}

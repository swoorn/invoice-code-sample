<?php

declare(strict_types=1);

namespace App\Tests\Modules\Shared\ValueObjects;

use App\Modules\Shared\ValueObjects\Money;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    public function testFromDollarsConvertsToCents(): void
    {
        $money = Money::fromDollars(10.00);

        self::assertSame(1000, $money->cents);
    }

    public function testFromDollarsRoundsHalfUp(): void
    {
        $money = Money::fromDollars(0.005);

        self::assertSame(1, $money->cents);
    }

    public function testZeroReturnsZeroCents(): void
    {
        self::assertSame(0, Money::zero()->cents);
    }

    public function testToDecimalConvertsToDollars(): void
    {
        $money = new Money(1099);

        self::assertSame(10.99, $money->toDecimal());
    }

    public function testEqualsReturnsTrueForSameAmount(): void
    {
        self::assertTrue(Money::fromDollars(5.00)->equals(Money::fromDollars(5.00)));
    }

    public function testEqualsReturnsFalseForDifferentAmount(): void
    {
        self::assertFalse(Money::fromDollars(5.00)->equals(Money::fromDollars(6.00)));
    }

    public function testAddReturnsSumOfAmounts(): void
    {
        $result = Money::fromDollars(3.00)->add(Money::fromDollars(2.00));

        self::assertTrue(Money::fromDollars(5.00)->equals($result));
    }

    public function testMultiplyScalesAmount(): void
    {
        $result = Money::fromDollars(4.00)->multiply(3);

        self::assertTrue(Money::fromDollars(12.00)->equals($result));
    }

    public function testSubtractReducesAmount(): void
    {
        $result = Money::fromDollars(10.00)->subtract(Money::fromDollars(3.00));

        self::assertTrue(Money::fromDollars(7.00)->equals($result));
    }

    public function testSubtractThrowsWhenResultIsNegative(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Money::fromDollars(3.00)->subtract(Money::fromDollars(5.00));
    }

    public function testNegativeCentsThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Money(-1);
    }
}

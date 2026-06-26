<?php

declare(strict_types=1);

namespace App\Tests\Modules\Invoices\Domain\Entities;

use App\Modules\Invoices\Domain\Entities\Product;
use App\Modules\Invoices\Domain\ValueObjects\ProductName;
use App\Modules\Invoices\Domain\ValueObjects\Quantity;
use App\Modules\Shared\ValueObjects\Money;
use PHPUnit\Framework\TestCase;

final class ProductTest extends TestCase
{
    public function testConstructorStoresValues(): void
    {
        $name = new ProductName('Widget');
        $quantity = new Quantity(3);
        $unitPrice = Money::fromDollars(9.99);

        $product = new Product($name, $quantity, $unitPrice);

        self::assertSame('Widget', $product->name()->value);
        self::assertSame(3, $product->quantity()->value);
        self::assertTrue($unitPrice->equals($product->unitPrice()));
    }

    public function testTotalPriceMultipliesUnitPriceByQuantity(): void
    {
        // $10 × 4 = $40
        $product = new Product(
            new ProductName('Widget'),
            new Quantity(4),
            Money::fromDollars(10.00)
        );

        self::assertTrue(Money::fromDollars(40.00)->equals($product->totalPrice()));
    }
}

<?php

namespace Modules\Invoices\Domain\Entites;

use Modules\Invoices\Domain\ValueObjects\Money;
use Modules\Invoices\Domain\ValueObjects\ProductName;
use Modules\Invoices\Domain\ValueObjects\Quantity;

class Product
{
    public function __construct(
        public readonly ProductName $name,
        public readonly Quantity $quantity,
        public readonly Money $unitPrice,
    ) {

    }

    public function totalUnitPrice(): Money
    {
        return new Money($this->unitPrice->cents * $this->quantity->value);
    }
}

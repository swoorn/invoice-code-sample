<?php

namespace Modules\Invoices\Domain\ValueObjects;

class ProductName
{
    public function __construct(public string $name)
    {
    }
}

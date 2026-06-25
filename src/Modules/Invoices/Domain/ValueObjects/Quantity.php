<?php

namespace Modules\Invoices\Domain\ValueObjects;

class Quantity
{
    public function __construct(public readonly int $value)
    {
    }
}

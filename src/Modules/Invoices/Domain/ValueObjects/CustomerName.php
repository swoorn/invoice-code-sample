<?php

namespace Modules\Invoices\Domain\ValueObjects;

class CustomerName
{
    public function __construct(public readonly string $name)
    {
        // validate length
    }
}

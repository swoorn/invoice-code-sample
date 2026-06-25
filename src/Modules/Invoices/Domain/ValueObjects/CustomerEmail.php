<?php

namespace Modules\Invoices\Domain\ValueObjects;

class CustomerEmail
{
    public function __construct(public readonly string $email)
    {
        // validate
    }
}

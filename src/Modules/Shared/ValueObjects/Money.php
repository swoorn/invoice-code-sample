<?php

namespace Modules\Invoices\Domain\ValueObjects;

class Money
{
    public function __construct(public readonly int $cents)
    {
    }
}

<?php

namespace App\Modules\Invoices\UseCases\CreateInvoice;

class CreateInvoiceResult
{
    public function __construct(
        public readonly string $id,
    )
    {
    }
}

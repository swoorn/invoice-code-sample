<?php

declare(strict_types=1);

namespace App\Modules\Invoices\UseCases\ViewInvoice;

final class InvoiceLineViewDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly int $quantity,
        public readonly int $unitPriceInCents,
        public readonly int $totalPriceInCents
    ) {}
}

<?php

declare(strict_types=1);

namespace App\Modules\Invoices\UseCases\ViewInvoice;

final class InvoiceViewDto
{
    /**
     * @param InvoiceLineViewDto[] $items
     */
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly string $customerName,
        public readonly string $customerEmail,
        public readonly array $items,
        public readonly int $totalPriceInCents
    ) {}
}

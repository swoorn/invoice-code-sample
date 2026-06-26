<?php

declare(strict_types=1);

namespace App\Modules\Invoices\Domain\Repositories;

use App\Modules\Invoices\Domain\Entities\Invoice;

interface InvoiceRepositoryInterface
{
    public function save(Invoice $invoice): void;
    public function findByIdWithItems(string $id): ?Invoice;
    public function findById(string $id): ?Invoice;
}

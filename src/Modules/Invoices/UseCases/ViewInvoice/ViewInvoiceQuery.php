<?php

declare(strict_types=1);

namespace App\Modules\Invoices\UseCases\ViewInvoice;

use Symfony\Component\Validator\Constraints as Assert;

final class ViewInvoiceQuery
{
    public function __construct(
        #[Assert\Uuid]
        public readonly string $id
    ) {}
}

<?php

declare(strict_types=1);

namespace App\Modules\Invoices\UseCases\SendInvoice;

use Symfony\Component\Validator\Constraints as Assert;

final class SendInvoiceCommand
{
    public function __construct(
        #[Assert\Uuid]
        public readonly string $invoiceId
    ) {}
}

<?php

declare(strict_types=1);

namespace App\Modules\Invoices\UseCases\MarkInvoiceAsSent;

use App\Modules\Invoices\Domain\Repositories\InvoiceRepositoryInterface;
use App\Modules\Notifications\Api\Events\ResourceDeliveredEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class MarkInvoiceAsSentHandler
{
    public function __construct(
        private readonly InvoiceRepositoryInterface $invoiceRepository
    ) {}

    public function __invoke(ResourceDeliveredEvent $event): void
    {
        $invoice = $this->invoiceRepository->findById($event->resourceId);

        if ($invoice === null) {
            return;
        }

        $invoice->markAsSent();
        $this->invoiceRepository->save($invoice);
    }
}

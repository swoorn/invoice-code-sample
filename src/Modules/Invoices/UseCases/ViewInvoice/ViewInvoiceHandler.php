<?php

declare(strict_types=1);

namespace App\Modules\Invoices\UseCases\ViewInvoice;

use App\Modules\Invoices\Domain\Repositories\InvoiceRepositoryInterface;
use App\Modules\Shared\Exceptions\DomainLogicException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ViewInvoiceHandler
{
    public function __construct(
        private readonly InvoiceRepositoryInterface $invoiceRepository
    ) {}

    public function __invoke(ViewInvoiceQuery $query): ?InvoiceViewDto
    {
        $invoice = $this->invoiceRepository->findByIdWithItems($query->id);

        if ($invoice === null) {
            throw new DomainLogicException(sprintf('Invoice with ID "%s" not found.', $query->id));
        }

        $items = [];
        foreach ($invoice->items() as $item) {
            $items[] = new InvoiceLineViewDto(
                id: $item->id()->toString(),
                name: $item->name()->value,
                quantity: $item->quantity()->value,
                unitPriceInCents: $item->unitPrice()->cents,
                totalPriceInCents: $item->totalPrice()->cents
            );
        }

        return new InvoiceViewDto(
            id: $invoice->id()->toString(),
            status: $invoice->status()->value,
            customerName: $invoice->customerName()->name,
            customerEmail: $invoice->customerEmail()->email,
            items: $items,
            totalPriceInCents: $invoice->totalPrice()->cents
        );
    }
}

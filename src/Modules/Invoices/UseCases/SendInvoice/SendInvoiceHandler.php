<?php

declare(strict_types=1);

namespace App\Modules\Invoices\UseCases\SendInvoice;

use App\Modules\Invoices\Domain\Repositories\InvoiceRepositoryInterface;
use App\Modules\Notifications\Api\Dtos\NotifyData;
use App\Modules\Notifications\Api\NotificationFacadeInterface;
use App\Modules\Shared\Exceptions\DomainLogicException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SendInvoiceHandler
{
    public function __construct(
        private readonly InvoiceRepositoryInterface $invoiceRepository,
        private readonly NotificationFacadeInterface $notificationFacade
    ) {}

    public function __invoke(SendInvoiceCommand $command): void
    {
        $invoice = $this->invoiceRepository->findById($command->invoiceId);

        if ($invoice === null) {
            throw new DomainLogicException(sprintf('Invoice with ID "%s" not found.', $command->invoiceId));
        }

        $invoice->markAsSending();

        $this->notificationFacade->notify(
            new NotifyData(
                $invoice->id(),
                $invoice->customerEmail()->email,
                'New invoice created',
                'Hello, you can download your new invoice here - LINK',
            ),
        );

        $this->invoiceRepository->save($invoice);
    }
}

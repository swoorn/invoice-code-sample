<?php

declare(strict_types=1);

namespace App\Modules\Invoices\UseCases\CreateInvoice;

use App\Modules\Invoices\Domain\Entities\Invoice;
use App\Modules\Invoices\Domain\Entities\Product;
use App\Modules\Invoices\Domain\Repositories\InvoiceRepositoryInterface;
use App\Modules\Invoices\Domain\ValueObjects\CustomerEmail;
use App\Modules\Invoices\Domain\ValueObjects\CustomerName;
use App\Modules\Invoices\Domain\ValueObjects\ProductName;
use App\Modules\Invoices\Domain\ValueObjects\Quantity;
use App\Modules\Shared\ValueObjects\Money;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
class CreateInvoiceHandler
{
    public function __construct(
        private readonly InvoiceRepositoryInterface $invoiceRepository
    ) {}

    public function __invoke(CreateInvoiceCommand $command): CreateInvoiceResult
    {
        $customerName = new CustomerName($command->customerName);
        $customerEmail = new CustomerEmail($command->customerEmail);

        $invoice = new Invoice($customerName, $customerEmail);

        foreach ($command->items as $itemDto) {
            $productName = new ProductName($itemDto->name);
            $quantity = new Quantity($itemDto->quantity);
            $unitPrice = new Money($itemDto->priceInCents);

            $product = new Product($productName, $quantity, $unitPrice);

            $invoice->addItem($product);
        }

        $this->invoiceRepository->save($invoice);

        return new CreateInvoiceResult($invoice->id()->toString());
    }
}

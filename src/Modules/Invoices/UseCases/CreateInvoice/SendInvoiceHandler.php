<?php

use Modules\Invoices\Domain\Entites\Invoice;
use Modules\Invoices\Domain\ValueObjects\CustomerEmail;
use Modules\Invoices\Domain\ValueObjects\CustomerName;

class CreateInvoiceHandler
{
    public function __construct()
    {
    }

    public function handle(CreateInvoiceCommand $command): void
    {
        $products = ProductFactory::fromMany($command->items);

        $invoice = new Invoice(
            CustomerName::from($command->customerName),
            CustomerEmail::from($command->customerEmail),
            $products
        );

        $this->invoiceRepository->persist($invoice);

    }
}

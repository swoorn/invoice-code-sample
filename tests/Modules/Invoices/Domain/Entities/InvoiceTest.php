<?php

declare(strict_types=1);

namespace App\Tests\Modules\Invoices\Domain\Entities;

use App\Modules\Invoices\Domain\Entities\Invoice;
use App\Modules\Invoices\Domain\Entities\Product;
use App\Modules\Invoices\Domain\Enums\StatusEnum;
use App\Modules\Invoices\Domain\ValueObjects\CustomerEmail;
use App\Modules\Invoices\Domain\ValueObjects\CustomerName;
use App\Modules\Invoices\Domain\ValueObjects\ProductName;
use App\Modules\Invoices\Domain\ValueObjects\Quantity;
use App\Modules\Shared\Exceptions\DomainLogicException;
use App\Modules\Shared\ValueObjects\Money;
use PHPUnit\Framework\TestCase;

final class InvoiceTest extends TestCase
{
    private function makeInvoice(array $items = []): Invoice
    {
        return new Invoice(
            new CustomerName('Acme Corp'),
            new CustomerEmail('billing@acme.com'),
            $items
        );
    }

    private function makeProduct(Money $unitPrice = null): Product
    {
        return new Product(
            new ProductName('Widget'),
            new Quantity(2),
            $unitPrice ?? Money::fromDollars(10.00)
        );
    }

    public function testNewInvoiceHasDraftStatus(): void
    {
        $invoice = $this->makeInvoice();

        self::assertSame(StatusEnum::Draft, $invoice->status());
    }

    public function testNewInvoiceStoresCustomerInfo(): void
    {
        $name = new CustomerName('Acme Corp');
        $email = new CustomerEmail('billing@acme.com');
        $invoice = new Invoice($name, $email);

        self::assertTrue($invoice->customerName()->equals($name));
        self::assertTrue($invoice->customerEmail()->equals($email));
    }

    public function testMarkAsSendingTransitionsDraftToSending(): void
    {
        $invoice = $this->makeInvoice([$this->makeProduct()]);

        $invoice->markAsSending();

        self::assertSame(StatusEnum::Sending, $invoice->status());
    }

    public function testMarkAsSendingThrowsWhenAlreadySending(): void
    {
        $invoice = $this->makeInvoice([$this->makeProduct()]);
        $invoice->markAsSending();

        $this->expectException(DomainLogicException::class);

        $invoice->markAsSending();
    }

    public function testMarkAsSendingThrowsWhenAlreadySentToClient(): void
    {
        $invoice = $this->makeInvoice([$this->makeProduct()]);
        $invoice->markAsSending();
        $invoice->markAsSent();

        $this->expectException(DomainLogicException::class);

        $invoice->markAsSending();
    }

    public function testMarkAsSendingThrowsWithNoItems(): void
    {
        $invoice = $this->makeInvoice();

        $this->expectException(DomainLogicException::class);

        $invoice->markAsSending();
    }

    public function testMarkAsSendingThrowsWhenProductHasZeroUnitPrice(): void
    {
        $invoice = $this->makeInvoice([$this->makeProduct(Money::zero())]);

        $this->expectException(DomainLogicException::class);

        $invoice->markAsSending();
    }

    public function testMarkAsSentTransitionsSendingToSentToClient(): void
    {
        $invoice = $this->makeInvoice([$this->makeProduct()]);
        $invoice->markAsSending();

        $invoice->markAsSent();

        self::assertSame(StatusEnum::SentToClient, $invoice->status());
    }

    public function testMarkAsSentThrowsWhenInDraft(): void
    {
        $invoice = $this->makeInvoice();

        $this->expectException(DomainLogicException::class);

        $invoice->markAsSent();
    }

    public function testMarkAsSentThrowsWhenAlreadySentToClient(): void
    {
        $invoice = $this->makeInvoice([$this->makeProduct()]);
        $invoice->markAsSending();
        $invoice->markAsSent();

        $this->expectException(DomainLogicException::class);

        $invoice->markAsSent();
    }

    public function testAddItemIncreasesCount(): void
    {
        $invoice = $this->makeInvoice();

        $invoice->addItem($this->makeProduct());

        self::assertCount(1, $invoice->items());
    }

    public function testAddSameItemTwiceDoesNotDuplicate(): void
    {
        $invoice = $this->makeInvoice();
        $product = $this->makeProduct();

        $invoice->addItem($product);
        $invoice->addItem($product);

        self::assertCount(1, $invoice->items());
    }

    public function testTotalPriceIsZeroForEmptyInvoice(): void
    {
        $invoice = $this->makeInvoice();

        self::assertTrue(Money::zero()->equals($invoice->totalPrice()));
    }

    public function testTotalPriceSumsAllProductPrices(): void
    {
        // 2 × $10 + 3 × $5 = $35
        $productA = new Product(new ProductName('Alpha'), new Quantity(2), Money::fromDollars(10.00));
        $productB = new Product(new ProductName('Beta'), new Quantity(3), Money::fromDollars(5.00));
        $invoice = $this->makeInvoice([$productA, $productB]);

        self::assertTrue(Money::fromDollars(35.00)->equals($invoice->totalPrice()));
    }
}

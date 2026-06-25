<?php

namespace Modules\Invoices\Domain\Entites;

use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\Traits\DomainEventsTrait;
use Modules\Invoices\Domain\ValueObjects\CustomerEmail;
use Modules\Invoices\Domain\ValueObjects\CustomerName;
use Modules\Invoices\Domain\ValueObjects\Money;

class Invoice
{
    use DomainEventsTrait;

    private int $id;
    private StatusEnum $status;
    private CustomerName $customerName;
    private CustomerEmail $customerEmail;
    /** @var Product[] */
    private array $items;
    private Money $totalPrice;

    public function __construct(
        CustomerName $customerName,
        CustomerEmail $customerEmail,
        array $items = []
    ) {
        $this->status = StatusEnum::Draft;
        $this->customerName = $customerName;
        $this->customerEmail = $customerEmail;
        $this->items = $items;
    }

    private function totalPrice(): Money
    {
        $totalPrice = new Money(0);
        foreach ($this->items as $item) {
            $totalPrice = new Money($this->totalPrice->cents + $item->totalUnitPrice()->cents);
        }
        return $totalPrice;
    }

}

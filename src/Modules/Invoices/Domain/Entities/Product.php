<?php

declare(strict_types=1);

namespace App\Modules\Invoices\Domain\Entities;

use App\Modules\Invoices\Domain\ValueObjects\ProductName;
use App\Modules\Invoices\Domain\ValueObjects\Quantity;
use App\Modules\Shared\ValueObjects\Money;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity]
#[ORM\Table(name: 'invoice_products')]
class Product
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: Invoice::class, inversedBy: 'items')]
    #[ORM\JoinColumn(name: 'invoice_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Invoice $invoice;

    #[ORM\Embedded(class: ProductName::class, columnPrefix: false)]
    private ProductName $name;

    #[ORM\Embedded(class: Quantity::class, columnPrefix: false)]
    private Quantity $quantity;

    #[ORM\Embedded(class: Money::class, columnPrefix: false)]
    private Money $unitPrice;

    public function __construct(
        ProductName $name,
        Quantity $quantity,
        Money $unitPrice
    ) {
        $this->id = Uuid::uuid4();
        $this->name = $name;
        $this->quantity = $quantity;
        $this->unitPrice = $unitPrice;
    }

    public function id(): UuidInterface
    {
        return $this->id;
    }

    public function name(): ProductName
    {
        return $this->name;
    }

    public function quantity(): Quantity
    {
        return $this->quantity;
    }

    public function unitPrice(): Money
    {
        return $this->unitPrice;
    }

    public function setInvoice(Invoice $invoice): void
    {
        $this->invoice = $invoice;
    }

    public function totalPrice(): Money
    {
        return $this->unitPrice->multiply($this->quantity->value);
    }
}

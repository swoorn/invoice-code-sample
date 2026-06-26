<?php

declare(strict_types=1);

namespace App\Modules\Invoices\Domain\Entities;

use App\Modules\Invoices\Domain\Enums\StatusEnum;
use App\Modules\Invoices\Domain\ValueObjects\CustomerEmail;
use App\Modules\Invoices\Domain\ValueObjects\CustomerName;
use App\Modules\Shared\Exceptions\DomainLogicException;
use App\Modules\Shared\Traits\DomainEventsTrait;
use App\Modules\Shared\ValueObjects\Money;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity]
#[ORM\Table(name: 'invoice')]
class Invoice
{
    use DomainEventsTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidInterface $id;

    #[ORM\Column(type: 'string', enumType: StatusEnum::class)]
    private StatusEnum $status;

    #[ORM\Embedded(class: CustomerName::class, columnPrefix: false)]
    private CustomerName $customerName;

    #[ORM\Embedded(class: CustomerEmail::class, columnPrefix: false)]
    private CustomerEmail $customerEmail;

    /** * @var Collection<int, Product>
     */
    #[ORM\OneToMany(
        mappedBy: 'invoice',
        targetEntity: Product::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $items;

    private Money $totalPrice;

    public function __construct(
        CustomerName $customerName,
        CustomerEmail $customerEmail,
        array $items = []
    ) {
        $this->id = Uuid::uuid4();
        $this->status = StatusEnum::Draft;
        $this->customerName = $customerName;
        $this->customerEmail = $customerEmail;
        $this->items = new ArrayCollection();
        foreach ($items as $item) {
            $this->addItem($item);
        }
    }

    public function id(): UuidInterface
    {
        return $this->id;
    }

    public function status(): StatusEnum
    {
        return $this->status;
    }

    public function customerName(): CustomerName
    {
        return $this->customerName;
    }

    public function customerEmail(): CustomerEmail
    {
        return $this->customerEmail;
    }

    /**
     * @return Collection<int, Product>|Product[]
     */
    public function items(): Collection
    {
        return $this->items;
    }

    public function markAsSending(): void
    {
        if ($this->status !== StatusEnum::Draft) {
            throw new DomainLogicException(sprintf(
                'Cannot mark invoice %s as sending from status "%s".',
                $this->id->toString(),
                $this->status->value,
            ));
        }

        if ($this->items->isEmpty()) {
            throw new DomainLogicException(sprintf(
                'Cannot send invoice %s because it contains no product lines.',
                $this->id->toString()
            ));
        }

        foreach ($this->items as $item) {
            if ($item->quantity()->value <= 0) {
                throw new DomainLogicException(sprintf(
                    'Invoice %s cannot be sent because product "%s" has an invalid quantity (%d). Must be greater than 0.',
                    $this->id->toString(),
                    $item->name()->value,
                    $item->quantity()->value
                ));
            }

            if ($item->unitPrice()->cents <= 0) {
                throw new DomainLogicException(sprintf(
                    'Invoice %s cannot be sent because product "%s" has an invalid unit price. Must be greater than 0.',
                    $this->id->toString(),
                    $item->name()->value
                ));
            }
        }

        $this->status = StatusEnum::Sending;
    }
    public function markAsSent(): void
    {
        if ($this->status !== StatusEnum::Sending) {
            throw new DomainLogicException(sprintf(
                'Cannot mark invoice %s as sent from status "%s".',
                $this->id->toString(),
                $this->status->value,
            ));
        }

        $this->status = StatusEnum::SentToClient;
    }

    public function addItem(Product $item): void
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setInvoice($this);
        }
    }

    public function totalPrice(): Money
    {
        $totalCents = Money::zero();
        foreach ($this->items as $item) {
            $totalCents = $totalCents->add($item->totalPrice());
        }
        return $totalCents;
    }
}

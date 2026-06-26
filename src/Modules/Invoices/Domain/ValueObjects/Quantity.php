<?php

declare(strict_types=1);

namespace App\Modules\Invoices\Domain\ValueObjects;

use App\Modules\Shared\Exceptions\DomainLogicException;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class Quantity
{
    #[ORM\Column(type: 'integer', name: 'quantity')]
    public readonly int $value;

    public function __construct(int $value)
    {
        if ($value <= 0) {
            throw new DomainLogicException('Quantity must be greater than zero.');
        }

        $this->value = $value;
    }
}

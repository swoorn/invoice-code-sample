<?php

declare(strict_types=1);

namespace App\Modules\Invoices\Domain\ValueObjects;

use App\Modules\Shared\Exceptions\DomainLogicException;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class ProductName
{
    #[ORM\Column(type: 'string', name: 'name')]
    public readonly string $value;

    public function __construct(string $value)
    {
        if (empty(trim($value))) {
            throw new DomainLogicException('Product name cannot be empty.');
        }

        $this->value = $value;
    }
}

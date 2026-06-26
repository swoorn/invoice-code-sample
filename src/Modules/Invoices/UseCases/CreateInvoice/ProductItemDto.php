<?php

declare(strict_types=1);

namespace App\Modules\Invoices\UseCases\CreateInvoice;

use Symfony\Component\Validator\Constraints as Assert;

class ProductItemDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'Product name should not be blank.')]
        public readonly string $name,

        #[Assert\GreaterThan(0, message: 'Quantity must be at least 1.')]
        public readonly int $quantity,

        #[Assert\GreaterThanOrEqual(0, message: 'Price cannot be negative.')]
        public readonly int $priceInCents
    ) {}
}

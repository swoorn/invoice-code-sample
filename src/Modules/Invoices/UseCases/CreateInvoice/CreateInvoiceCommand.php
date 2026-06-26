<?php

declare(strict_types=1);

namespace App\Modules\Invoices\UseCases\CreateInvoice;

use Symfony\Component\Validator\Constraints as Assert;

class CreateInvoiceCommand
{
    /**
     * @param ProductItemDto[] $items
     */
    public function __construct(
        #[Assert\NotBlank(message: 'Customer name is required.')]
        public readonly string $customerName,

        #[Assert\NotBlank(message: 'Email is required.')]
        #[Assert\Email(message: 'The email {{ value }} is not a valid email.')]
        public readonly string $customerEmail,

        #[Assert\Valid]
        public readonly array $items
    ) {}

    public static function fromArray(array $data): self
    {
        $items = [];
        foreach ($data['items'] ?? [] as $item) {
            $items[] = new ProductItemDto(
                name: (string) ($item['name'] ?? ''),
                quantity: (int) ($item['quantity'] ?? 0),
                priceInCents: (int) ($item['priceInCents'] ?? 0)
            );
        }

        return new self(
            customerName: (string) ($data['customerName'] ?? ''),
            customerEmail: (string) ($data['customerEmail'] ?? ''),
            items: $items
        );
    }
}

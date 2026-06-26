<?php

declare(strict_types=1);

namespace App\Modules\Invoices\Domain\ValueObjects;

use App\Modules\Shared\Exceptions\DomainLogicException;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class CustomerName
{
    private const MIN_LENGTH = 2;
    private const MAX_LENGTH = 255;

    public function __construct(
        #[ORM\Column(name: 'customer_name', type: 'string', length: 255)]
        public readonly string $name,
    ) {
        $this->ensureIsValidName($name);
    }

    private function ensureIsValidName(string $name): void
    {
        $trimmedName = trim($name);

        if (empty($trimmedName)) {
            throw new DomainLogicException('Customer name cannot be empty.');
        }

        $length = mb_strlen($trimmedName);
        if ($length < self::MIN_LENGTH || $length > self::MAX_LENGTH) {
            throw new DomainLogicException(sprintf(
                'Customer name must be between %d and %d characters long. Got %d.',
                self::MIN_LENGTH,
                self::MAX_LENGTH,
                $length
            ));
        }
    }

    public function equals(CustomerName $other): bool
    {
        return trim($this->name) === trim($other->name);
    }
}

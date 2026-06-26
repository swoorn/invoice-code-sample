<?php

declare(strict_types=1);

namespace App\Modules\Invoices\Domain\ValueObjects;

use App\Modules\Shared\Exceptions\DomainLogicException;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class CustomerEmail
{
    public function __construct(
        #[ORM\Column(name: 'customer_email', type: 'string', length: 255)]
        public readonly string $email,
    ) {
        $this->ensureIsValidEmail($email);
    }

    private function ensureIsValidEmail(string $email): void
    {
        $cleanEmail = trim(strtolower($email));

        if (empty($cleanEmail) || !filter_var($cleanEmail, FILTER_VALIDATE_EMAIL)) {
            throw new DomainLogicException(sprintf(
                'The email "%s" is not a valid email address.',
                $email
            ));
        }
    }

    public function equals(CustomerEmail $other): bool
    {
        return strtolower($this->email) === strtolower($other->email);
    }
}

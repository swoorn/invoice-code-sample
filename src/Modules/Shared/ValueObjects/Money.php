<?php

declare(strict_types=1);

namespace App\Modules\Shared\ValueObjects;

use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Embeddable]
final class Money
{
    /**
     * @throws InvalidArgumentException If the amount is negative.
     */
    public function __construct(
        #[ORM\Column(type: 'integer', name: 'cents')]
        public readonly int $cents
    ) {
        $this->ensureIsValid($this->cents);
    }

    public static function fromDollars(float $dollars): self
    {
        return new self((int) round($dollars * 100));
    }

    public static function zero(): self
    {
        return new self(0);
    }

    private function ensureIsValid(int $cents): void
    {
        if ($cents < 0) {
            throw new InvalidArgumentException('Money amount cannot be negative.');
        }
    }

    public function toDecimal(): float
    {
        return $this->cents / 100;
    }

    public function equals(self $other): bool
    {
        return $this->cents === $other->cents;
    }

    public function add(self $other): self
    {
        return new self($this->cents + $other->cents);
    }

    public function multiply(int $multiplier): self
    {
        return new self($this->cents * $multiplier);
    }

    public function subtract(self $other): self
    {
        return new self($this->cents - $other->cents);
    }
}

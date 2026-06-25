<?php

namespace Modules\Invoices\Domain\Traits;

trait DomainEventsTrait
{
    /**
     * @var DomainEvent[]
     */
    private $domainEventList = [];

    /**
     * @param DomainEvent $event
     */
    public function registerDomainEvent(DomainEvent $event)
    {
        $this->domainEventList[] = $event;
    }

    /**
     * @return DomainEvent[]
     */
    public function getDomainEvents(): array
    {
        return $this->domainEventList;
    }

    public function clearDomainEvents(): void
    {
        $this->domainEventList = [];
    }
}

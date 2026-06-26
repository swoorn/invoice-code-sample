<?php

namespace App\Modules\Notifications\Api\Events;

final class ResourceDeliveredEvent
{
    public function __construct(
        public readonly string $resourceId
    ) {}
}

<?php

namespace App\Modules\Notifications\UseCases\SendEmailNotification;

use Ramsey\Uuid\UuidInterface;

final readonly class SendEmailNotificationCommand
{
    public function __construct(
        public UuidInterface $resourceId,
        public string $toEmail,
        public string $subject,
        public string $message,
    ) {}
}

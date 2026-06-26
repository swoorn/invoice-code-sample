<?php

declare(strict_types=1);

namespace App\Modules\Notifications\UseCases\SendEmailNotification;

use App\Modules\Notifications\Api\Events\ResourceDeliveredEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final class SendEmailNotificationHandler
{
    public function __construct(
        private readonly LoggerInterface $notificationWorkerLogger,
        private readonly MessageBusInterface $eventBus
    ) {}

    public function __invoke(SendEmailNotificationCommand $command): void
    {
        $this->notificationWorkerLogger->info('Asynchronous notification processed successfully.', [
            'channel' => 'notification_worker',
            'invoice_id' => $command->resourceId,
            'recipient' => [
                'email' => $command->toEmail,
            ],
            'subject' => $command->subject,
            'message' => $command->message,
            'timestamp' => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM)
        ]);

        $this->eventBus->dispatch(new ResourceDeliveredEvent($command->resourceId->toString()));
    }
}

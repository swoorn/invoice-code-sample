<?php

declare(strict_types=1);

namespace App\Modules\Notifications\Facade;

use App\Modules\Notifications\Api\Dtos\NotifyData;
use App\Modules\Notifications\Api\NotificationFacadeInterface;
use App\Modules\Notifications\UseCases\SendEmailNotification\SendEmailNotificationCommand;
use Symfony\Component\Messenger\MessageBusInterface;

final class NotificationFacade implements NotificationFacadeInterface
{
    public function __construct(
        private readonly MessageBusInterface $commandBus
    ) {}

    public function notify(NotifyData $data): void
    {
        $notificationTask = new SendEmailNotificationCommand(
            $data->resourceId,
            $data->toEmail,
            $data->subject,
            $data->message
        );

        // Dispatches to the bus. Because of messenger.yaml routing,
        // this will end up written into the doctrine transport queue table.
        $this->commandBus->dispatch($notificationTask);
    }
}

<?php

namespace App\Modules\Notifications\Api;


use App\Modules\Notifications\Api\Dtos\NotifyData;

interface NotificationFacadeInterface
{
    public function notify(NotifyData $data): void;
}

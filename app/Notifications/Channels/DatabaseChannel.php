<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Channels\DatabaseChannel as BaseChannel;
use Illuminate\Notifications\Notification;

/**
 * Laravel's database channel writes a UUID into `id`, but our notifications table uses an
 * auto-increment id plus user_id. Drop the UUID and fill user_id so the row lands in the inbox.
 */
class DatabaseChannel extends BaseChannel
{
    protected function buildPayload($notifiable, Notification $notification)
    {
        $payload = parent::buildPayload($notifiable, $notification);
        unset($payload['id']);
        $payload['user_id'] = $notifiable->getKey();

        return $payload;
    }
}

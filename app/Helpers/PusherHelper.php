<?php

namespace App\Helpers;

use Pusher\Pusher;
use Pusher\PusherException;

class PusherHelper
{
    private $instance;
    private $channel;
    private $event;

    /**
     * @throws PusherException
     */
    public function __construct(string $channel, string $event)
    {
        $this->channel = $channel;
        $this->event = $event;
        $this->gI();
    }

    public function sendEvent(array $data): void
    {
        $this->instance->trigger($this->channel, $this->event, $data);
    }

    /**
     * @throws PusherException
     */
    public function gI(): Pusher
    {
        if (!$this->instance) {
            $this->instance = new Pusher(
                env('PUSHER_APP_KEY'),
                env('PUSHER_APP_SECRET'),
                env('PUSHER_APP_ID'),
                [
                    'cluster' => env('PUSHER_APP_CLUSTER'),
                    'useTLS' => true
                ]
            );
        }
        return $this->instance;
    }
}

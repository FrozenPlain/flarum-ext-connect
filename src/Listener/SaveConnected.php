<?php

namespace FrozenPlain\Connect\Listener;

use Carbon\Carbon;
use Flarum\User\Event\Saving;
use Illuminate\Support\Arr;

class SaveConnected
{
    public function handle(Saving $event)
    {
        $attributes = Arr::get($event->data, 'attributes', []);

        if (!array_key_exists('frozenplainConnected', $attributes)) {
            return;
        }

        // Connecting decides which forum account a frozenplain.com sign-in opens
        $event->actor->assertAdmin();

        if (!$attributes['frozenplainConnected']) {
            $event->user->frozenplain_connected_at = null;
        } elseif (!$event->user->frozenplain_connected_at) {
            $event->user->frozenplain_connected_at = Carbon::now();
        }
    }
}

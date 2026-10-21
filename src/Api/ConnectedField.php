<?php

namespace FrozenPlain\Connect\Api;

use Carbon\Carbon;
use Flarum\Api\Context;
use Flarum\Api\Schema;
use Flarum\User\User;

/**
 * The user attribute frozenplain.com reads and writes to connect or disconnect a forum account.
 * Admins only: connecting decides which forum account a frozenplain.com sign-in opens.
 */
class ConnectedField
{
    public function __invoke(): array
    {
        $isAdmin = fn (User $user, Context $context) => $context->getActor()->isAdmin();

        return [
            Schema\Boolean::make('frozenplainConnected')
                ->visible($isAdmin)
                ->writable($isAdmin)
                ->get(fn (User $user) => $user->frozenplain_connected_at !== null)
                ->set(function (User $user, $value) {
                    if (!$value) {
                        $user->frozenplain_connected_at = null;
                    } elseif (!$user->frozenplain_connected_at) {
                        $user->frozenplain_connected_at = Carbon::now();
                    }
                }),
        ];
    }
}

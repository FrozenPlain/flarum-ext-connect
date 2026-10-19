<?php

use Flarum\Database\Migration;

return Migration::addColumns('users', [
    'frozenplain_connected_at' => ['dateTime', 'nullable' => true],
]);

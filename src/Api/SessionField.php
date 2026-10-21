<?php

namespace FrozenPlain\Connect\Api;

use Flarum\Api\Context;
use Flarum\Api\Schema;

// Whether this request was signed in by AuthenticateWithFrozenPlain
class SessionField
{
    public function __invoke(): array
    {
        return [
            Schema\Boolean::make('frozenplainSession')
                ->get(fn ($forum, Context $context) => (bool) $context->request->getAttribute('frozenplainSession')),
        ];
    }
}

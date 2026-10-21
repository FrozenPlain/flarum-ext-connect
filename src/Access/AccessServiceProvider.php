<?php

namespace FrozenPlain\Connect\Access;

use Flarum\Foundation\AbstractServiceProvider;

class AccessServiceProvider extends AbstractServiceProvider
{
    public function register(): void
    {
        // Shared, so the policies see what MarkApiKeyRequest recorded for this request
        $this->container->singleton(ApiKeyRequest::class);
    }
}

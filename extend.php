<?php

use Flarum\Api\Resource\ForumResource;
use Flarum\Api\Resource\UserResource;
use Flarum\Discussion\Discussion;
use Flarum\Extend;
use Flarum\Http\Middleware\AuthenticateWithHeader;
use Flarum\Http\Middleware\AuthenticateWithSession;
use Flarum\Tags\Tag;
use FrozenPlain\Connect\Access\AccessServiceProvider;
use FrozenPlain\Connect\Access\ReviewDiscussionPolicy;
use FrozenPlain\Connect\Access\ReviewsTagPolicy;
use FrozenPlain\Connect\Api\ConnectedField;
use FrozenPlain\Connect\Api\SessionField;
use FrozenPlain\Connect\Middleware\AuthenticateWithFrozenPlain;
use FrozenPlain\Connect\Middleware\MarkApiKeyRequest;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__ . '/js/dist/forum.js')
        ->css(__DIR__ . '/less/forum.less'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__ . '/js/dist/admin.js'),

    new Extend\Locales(__DIR__ . '/locale'),

    (new Extend\Settings())
        ->default('frozenplain-connect.siteUrl', 'https://frozenplain.com')
        ->serializeToForum('frozenplainSiteUrl', 'frozenplain-connect.siteUrl'),

    // frozenplain.com connects and disconnects accounts through the API, as an admin
    (new Extend\ApiResource(UserResource::class))
        ->fields(ConnectedField::class),

    // Lets the forum JS swap the log-out button for frozenplain.com's sign-out
    (new Extend\ApiResource(ForumResource::class))
        ->fields(SessionField::class),

    (new Extend\Middleware('forum'))
        ->insertAfter(AuthenticateWithSession::class, AuthenticateWithFrozenPlain::class),
    (new Extend\Middleware('admin'))
        ->insertAfter(AuthenticateWithSession::class, AuthenticateWithFrozenPlain::class),
    (new Extend\Middleware('api'))
        ->insertAfter(AuthenticateWithSession::class, AuthenticateWithFrozenPlain::class)
        ->insertAfter(AuthenticateWithHeader::class, MarkApiKeyRequest::class),

    // Reviews are only posted through frozenplain.com, which checks the customer owns the product
    (new Extend\ServiceProvider())
        ->register(AccessServiceProvider::class),
    (new Extend\Policy())
        ->modelPolicy(Tag::class, ReviewsTagPolicy::class)
        ->modelPolicy(Discussion::class, ReviewDiscussionPolicy::class),
];

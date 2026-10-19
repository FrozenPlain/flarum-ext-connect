<?php

use Flarum\Api\Serializer\ForumSerializer;
use Flarum\Api\Serializer\UserSerializer;
use Flarum\Discussion\Discussion;
use Flarum\Extend;
use Flarum\Http\Middleware\AuthenticateWithHeader;
use Flarum\Http\Middleware\AuthenticateWithSession;
use Flarum\Tags\Tag;
use Flarum\User\Event\Saving;
use FrozenPlain\Connect\Access\AccessServiceProvider;
use FrozenPlain\Connect\Access\ReviewDiscussionPolicy;
use FrozenPlain\Connect\Access\ReviewsTagPolicy;
use FrozenPlain\Connect\Listener\SaveConnected;
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
    (new Extend\Event())
        ->listen(Saving::class, SaveConnected::class),

    (new Extend\ApiSerializer(UserSerializer::class))
        ->attributes(function (UserSerializer $serializer, $user) {
            return $serializer->getActor()->isAdmin()
                ? ['frozenplainConnected' => $user->frozenplain_connected_at !== null]
                : [];
        }),

    // Lets the forum JS swap the log-out button for frozenplain.com's sign-out
    (new Extend\ApiSerializer(ForumSerializer::class))
        ->attributes(function (ForumSerializer $serializer) {
            return ['frozenplainSession' => (bool) $serializer->getRequest()->getAttribute('frozenplainSession')];
        }),

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

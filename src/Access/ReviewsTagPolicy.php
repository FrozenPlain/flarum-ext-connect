<?php

namespace FrozenPlain\Connect\Access;

use Flarum\Tags\Tag;
use Flarum\User\Access\AbstractPolicy;
use Flarum\User\User;

/**
 * Reviews are only started through frozenplain.com, which checks the customer owns the product.
 * Forced, so neither tag permissions nor group memberships can open the Reviews tag to posting
 * from the forum itself. Admins can still post and move discussions there.
 */
class ReviewsTagPolicy extends AbstractPolicy
{
    const REVIEWS_TAG_SLUG = 'reviews';

    public function __construct(protected ApiKeyRequest $apiKeyRequest)
    {
    }

    // Also covers adding the tag to an existing discussion: addToDiscussion asks startDiscussion
    public function startDiscussion(User $actor, Tag $tag): ?string
    {
        if ($tag->slug !== self::REVIEWS_TAG_SLUG || $actor->isAdmin()) {
            return null;
        }

        return $this->apiKeyRequest->active ? $this->forceAllow() : $this->forceDeny();
    }
}

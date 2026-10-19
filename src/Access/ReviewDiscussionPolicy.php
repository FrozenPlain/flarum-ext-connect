<?php

namespace FrozenPlain\Connect\Access;

use Flarum\Discussion\Discussion;
use Flarum\User\Access\AbstractPolicy;
use Flarum\User\User;

// A review stays on the product it was written for: authors can't retag it onto another product
class ReviewDiscussionPolicy extends AbstractPolicy
{
    public function tag(User $actor, Discussion $discussion)
    {
        if ($actor->isAdmin()) {
            return;
        }

        if ($discussion->tags->contains('slug', ReviewsTagPolicy::REVIEWS_TAG_SLUG)) {
            return $this->forceDeny();
        }
    }
}

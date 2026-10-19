<?php

namespace FrozenPlain\Connect\Access;

/**
 * Whether the current request authenticated with an API key. Only frozenplain.com holds one, and
 * it checks that the customer owns the product before posting a review as them.
 */
class ApiKeyRequest
{
    public $active = false;
}

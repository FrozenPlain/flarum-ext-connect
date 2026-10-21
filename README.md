Flarum extension that lets customers optionally sign in to the [FrozenPlain forum](https://forum.frozenplain.com) with their [frozenplain.com](https://frozenplain.com) account. It also keeps the Reviews tag for reviews posted through frozenplain.com, which checks the customer owns the product. It's specific to FrozenPlain's setup, so it's unlikely to be useful elsewhere.

Version 2.x is for Flarum 2.0; version 1.x was for Flarum 1.8. To rebuild the JavaScript after changing it: `cd js && npm install && npm run build`.

Credit to [JWT Cookie Login](https://github.com/clarkwinkelmann/flarum-ext-jwt-cookie-login) for the approach.

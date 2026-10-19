<?php

namespace FrozenPlain\Connect\Middleware;

use Dflydev\FigCookies\FigRequestCookies;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Flarum\Http\RequestUtil;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

/**
 * Signs in the forum account a customer has connected to their frozenplain.com account.
 *
 * frozenplain.com sets a short RS256 JWT cookie (sub = forum user ID) on the shared parent domain
 * after the customer deliberately connects. Only users with frozenplain_connected_at are accepted,
 * so disconnecting on frozenplain.com takes effect immediately. Accounts are never created here.
 */
class AuthenticateWithFrozenPlain implements MiddlewareInterface
{
    const COOKIE_NAME = 'fp_forum_sso';
    const AUDIENCE = 'frozenplain-forum';

    protected $settings;

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = $this->connectedUser($request);

        if ($user) {
            $user->updateLastSeen()->save();

            $request = RequestUtil::withActor($request, $user)
                ->withAttribute('frozenplainSession', true);

            // When the signed-in account changes, replace the CSRF token so a window still open
            // as someone else can't act as this user
            $session = $request->getAttribute('session');
            if ($session && $session->get('frozenplainUserId') !== $user->id) {
                $session->invalidate();
                $session->regenerateToken();
                $session->put('frozenplainUserId', $user->id);
            }
        }

        return $handler->handle($request);
    }

    protected function connectedUser(ServerRequestInterface $request): ?User
    {
        $jwt = FigRequestCookies::get($request, self::COOKIE_NAME)->getValue();
        $publicKey = $this->settings->get('frozenplain-connect.publicKey');

        if (!$jwt || !$publicKey) {
            return null;
        }

        try {
            // Pinning RS256 here rejects tokens claiming any other algorithm
            $payload = JWT::decode($jwt, new Key($publicKey, 'RS256'));
        } catch (Throwable $e) {
            return null;
        }

        if (($payload->aud ?? null) !== self::AUDIENCE || !isset($payload->sub) || !ctype_digit((string) $payload->sub)) {
            return null;
        }

        return User::query()
            ->where('id', (int) $payload->sub)
            ->whereNotNull('frozenplain_connected_at')
            ->first();
    }
}

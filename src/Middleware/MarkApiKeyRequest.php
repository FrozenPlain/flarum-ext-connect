<?php

namespace FrozenPlain\Connect\Middleware;

use FrozenPlain\Connect\Access\ApiKeyRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

// Policies don't see the request, so this records what AuthenticateWithHeader found
class MarkApiKeyRequest implements MiddlewareInterface
{
    protected $apiKeyRequest;

    public function __construct(ApiKeyRequest $apiKeyRequest)
    {
        $this->apiKeyRequest = $apiKeyRequest;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->apiKeyRequest->active = $request->getAttribute('apiKey') !== null;

        return $handler->handle($request);
    }
}

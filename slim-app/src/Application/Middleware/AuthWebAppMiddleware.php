<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Auth0\SDK\Contract\Auth0Interface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Response;

readonly class AuthWebAppMiddleware implements Middleware
{
    public function __construct(protected Auth0Interface $sdk, private LoggerInterface $logger)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function process(Request $request, RequestHandler $handler): ResponseInterface
    {
        $this->logger->info('Authentication starts');
        $session = $this->sdk->getCredentials();

        if (is_null($session)) {
            $this->sdk->clear();

            $authUrl = $this->sdk->login(getenv('APP_BASE_URL').'/auth-callback');
            return (new Response())->withHeader('Location', $authUrl);
        }

        return $handler->handle($request);
    }
}

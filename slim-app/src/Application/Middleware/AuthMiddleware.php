<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Auth0\SDK\Auth0;
use Auth0\SDK\Contract\Auth0Interface;
use Auth0\SDK\Exception\InvalidTokenException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Response;

readonly class AuthMiddleware implements Middleware
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
        $response = (new Response())->withHeader('Content-Type', 'application/json');

        $token = $this->sdk->getBearerToken(
            get: ['token'],
            server: ['HTTP_AUTHORIZATION']
        );

        if ($token) {
            try {
                $token->validate();
                return $response->withStatus(StatusCode::STATUS_UNAUTHORIZED, 'Successfully authorized');
            } catch (InvalidTokenException $e) {
                $response->getBody()->write(json_encode(['Auth error' => $e->getMessage()]));
            } catch (\Throwable $e) {
                $this->logger->error($e->getMessage());
                $response->getBody()->write(json_encode(['App error' => $e->getMessage()]));
            }
        } else {
            $response->getBody()->write(json_encode(['Response' => 'Request is not authorized']));
        }

        return $response->withStatus(StatusCode::STATUS_UNAUTHORIZED, 'Unauthorized');
    }
}

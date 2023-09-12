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
        $response = new Response();

        $token = $this->sdk->getBearerToken(
            get: ['token'],
            server: ['HTTP_AUTHORIZATION']
        );

        try {
            $token->validate();
        } catch (InvalidTokenException $e) {
            $this->logger->error($e->getMessage());
        } catch (\Throwable $e) {
            $response->getBody()->write(json_encode(['msg' => 'Unauthorized']));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(StatusCode::STATUS_UNAUTHORIZED, 'Unauthorized');
        }

        return $response;
    }
}

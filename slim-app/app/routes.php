<?php

declare(strict_types=1);

use App\Application\Actions\Html\PageOneAction;
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use App\Application\Middleware\AuthMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $c = $app->getContainer();
    /** @var LoggerInterface $logger */
    $logger = $c->get(LoggerInterface::class);
    $auth0Sdk = $c->get('Auth0SdkM2M');

    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });

    // This group contains the routes which require client authorization
    $app->group('/auth-required', function (Group $group){
        $group->get('/resource-a', function (Request $request, Response $response) {
            $response->getBody()->write('Resource A content received');
            return $response;
        });
        $group->get('/resource-b', function (Request $request, Response $response) {
            $response->getBody()->write('Resource B content received');
            return $response;
        });
    })->addMiddleware(new AuthMiddleware($auth0Sdk, $logger));

    // This group contains the routes which use Authorization Code Flow with Proof Key for Code Exchange (PKCE)
    $app->group('/no-auth', function (Group $group){
        $group->get('/page-one', PageOneAction::class);
    });
};

<?php

declare(strict_types=1);

use Auth0\SDK\Auth0;
use App\Application\Actions\Html\{LoginAction, PageOneAction, ProfileAction};
use Fig\Http\Message\StatusCodeInterface;
use App\Application\Actions\User\{ViewUserAction, ListUsersAction};
use App\Application\Middleware\{AuthClientCredentialsMiddleware, AuthWebAppMiddleware};
use Psr\Http\Message\{ResponseInterface as Response, ServerRequestInterface as Request};
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $c = $app->getContainer();
    /** @var LoggerInterface $logger */
    $logger = $c->get(LoggerInterface::class);
    $auth0SdkM2M = $c->get('Auth0SdkM2M');
    /** @var Auth0 $auth0SdkWebApp */
    $auth0SdkWebApp = $c->get('Auth0SdkWebApp');

    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) use ($auth0SdkWebApp) {
        $response->getBody()->write('Hello world!');
        return $this->get('view')
            ->render($response, 'home.twig', ['userData' => var_export($auth0SdkWebApp->getCredentials(), true)]);
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });

    $app->get('/auth-callback', function (Request $request, Response $response) use ($auth0SdkWebApp) {
        $result = $auth0SdkWebApp->exchange(getenv('APP_BASE_URL').'/auth-callback');
        return $response->withStatus(StatusCodeInterface::STATUS_FOUND)->withHeader('Location', '/');
    });
    $app->get('/profile', ProfileAction::class);
    $app->get('/login', LoginAction::class);
    $app->get('/logout', function (Request $request, Response $response) use ($auth0SdkWebApp) {
        return $response->withHeader('Location', $auth0SdkWebApp->logout(getenv('APP_BASE_URL', ).'/'));
    });

    // This group contains the routes which require client authorization
    $app->group('/client-credentials', function (Group $group){
        $group->get('/resource-a', function (Request $request, Response $response) {
            $response->getBody()->write('Resource A content received');
            return $response;
        });
        $group->get('/resource-b', function (Request $request, Response $response) {
            $response->getBody()->write('Resource B content received');
            return $response;
        });
    })->addMiddleware(new AuthClientCredentialsMiddleware($auth0SdkM2M, $logger));

    // This group contains the routes which use Authorization Code Flow with Proof Key for Code Exchange (PKCE)
    $app->group('/web-app-auth', function (Group $group){
        $group->get('/page-one', PageOneAction::class);
    })->addMiddleware(new AuthWebAppMiddleware($auth0SdkWebApp, $logger));
};

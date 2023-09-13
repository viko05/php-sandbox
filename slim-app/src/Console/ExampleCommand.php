<?php

namespace App\Console;

use App\Auth\DataObjects\AccessToken;
use Auth0\SDK\Auth0;
use Auth0\SDK\Configuration\SdkConfiguration;
use Auth0\SDK\Exception\ConfigurationException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;

class ExampleCommand extends Command
{
    protected function configure(): void
    {
        parent::configure();

        $this->setName('example');
        $this->setDescription('A sample command');
    }

    /**
     * Usage: php cli/example.php example
     * Machine to machine authorization flow demo
     *
     * @throws ConfigurationException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Machine to machine authentication flow example</info>');

        $authConf = new SdkConfiguration(
            strategy: SdkConfiguration::STRATEGY_API,
            domain: getenv('OAUTH_API_DOMAIN'),
            clientId: getenv('CLIENT_ID'),
            clientSecret: getenv('CLIENT_SECRET'),
            audience: [getenv('OAUTH_API_IDENTIFIER')],
        );

        $sdk = new Auth0($authConf);
        // Request access token from OAuth issuer server
        $resp = $sdk->authentication()->oauthToken('client_credentials');
        // @todo: handle failed auth
        $accessToken = new AccessToken($resp);

        // Now let's call the api with this token
        $http = HttpClient::createForBaseUri('http://nginx');
        $resp = $http->request('GET', '/auth-required/resource-a', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$accessToken->accessToken}",
            ],
            'extra' => ['XDEBUG_SESSION' => 'PHPSTORM'],
        ]);

        $output->writeln(sprintf('<info>Auth status code %s</info>', $resp->getStatusCode()));

        // The error code, 0 on success
        return 0;
    }
}

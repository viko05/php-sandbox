<?php

declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use Auth0\SDK\Auth0;
use Auth0\SDK\Configuration\SdkConfiguration;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
        'Auth0SdkM2M' => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);
            $sdkConf = $settings->get('Auth0M2MClient');

            $authConf = new SdkConfiguration(
                strategy: $sdkConf['strategy'],
                domain: $sdkConf['domain'],
                clientId: $sdkConf['clientId'],
                clientSecret: $sdkConf['clientSecret'],
                audience: $sdkConf['audience'],
            );

            return new Auth0($authConf);
        },
    ]);
};

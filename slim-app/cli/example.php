#!/usr/bin/env php
<?php

require __DIR__.'/../vendor/autoload.php';

use App\Console\ExampleCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Dotenv\Dotenv;

$env = (new Dotenv())->usePutenv();
$env->loadEnv(__DIR__.'/.env');

$application = new Application();

$application->add(new ExampleCommand());

$application->run();

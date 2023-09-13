<?php

declare(strict_types=1);

namespace App\Application\Actions\Html;

use App\Application\Actions\Action;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;

abstract class PageAction extends Action
{
    public function __construct(protected LoggerInterface $logger, protected Twig $view)
    {
        parent::__construct($logger);
    }
}

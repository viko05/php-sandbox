<?php

declare(strict_types=1);

namespace App\Application\Actions\Html;

use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

/**
 * @property Twig $view
 */
class PageOneAction extends PageAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        return $this->view->render($this->response, 'page-one.twig');
    }
}

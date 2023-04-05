<?php

namespace App\Controller;

use Miniframe\Core\AbstractController;
use Miniframe\Core\Response;
use Miniframe\Response\PhpResponse;


class Index extends AbstractController
{
    public function main(): Response
    {
        return new PhpResponse(__DIR__ . '/../../templates/index.html.php', [

        ]);
    }
}
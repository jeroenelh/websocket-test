<?php

namespace App\Controller;

use App\Service\WebsocketServer;
use Miniframe\Core\AbstractController;
use Miniframe\Core\Response;

class Server extends AbstractController
{
    public function main(): Response
    {
        new WebsocketServer();
    }
}


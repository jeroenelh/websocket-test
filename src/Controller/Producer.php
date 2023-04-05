<?php

namespace App\Controller;

use App\Service\WebsocketServer;
use Miniframe\Core\AbstractController;
use Miniframe\Core\Response;
use Socket\Raw\Factory;

class Producer extends AbstractController
{
    public function main(): Response
    {
        $factory = new Factory();
        $socket = $factory->createClient('tcp://0.0.0.0:8080');
        while (true) {
            $socket->write(rand(1,100));
            sleep(3);
        }
    }
}


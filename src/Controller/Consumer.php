<?php

namespace App\Controller;

use App\Service\WebsocketServer;
use Miniframe\Core\AbstractController;
use Miniframe\Core\Response;
use Socket\Raw\Factory;

class Consumer extends AbstractController
{
    public function main(): Response
    {
        $factory = new Factory();
        $socket = $factory->createClient('tcp://0.0.0.0:8080');
        while (true) {
            if ($socket->selectRead()) {
                echo "Message from server: ".$socket->read(1024)."\n";
            }
            sleep(1);
        }
    }
}


<?php

namespace App\Service;

use Socket\Raw\Factory;
use Socket\Raw\Socket;

class WebsocketServer {

    /** @var Socket */
    private $socket;

    /** @var Socket[] */
    private $clients = [];

    public function __construct()
    {
        $this->socket = $this->createSocket();

        while (true) {
            $this->handleServerMessage();

            foreach ($this->clients as $client)
            {
                $this->handleClientMessage($client);
            }
        }
    }

    private function handleServerMessage(): void
    {
        if ($this->socket->selectRead()) {
            $clientSocket = $this->socket->accept();
            $this->doHandshake($clientSocket, 'localhost', 8080);
            $clientSocket->setBlocking(false);
            $this->clients[] = $clientSocket;

            echo"client connected: ".$clientSocket->getPeerName()."\n";
        } else {
            usleep(100000);
        }
    }

    private function handleClientMessage(Socket $client): void
    {
        if ($client->selectRead() === false) {
//            echo "No message for client: ".$client->getPeerName()."\n";
            return;
        }



        $data = $client->recv(2048, MSG_DONTWAIT); // MSG_DONTWAIT
        echo "Message for client: ".$client->getPeerName()." '".$data."'\n";
//        $data = $this->unmask($data);
        if ($data === null) {
            return;
        }

        if ($data === 0 || $data == 'é') {
            // Disconnected
            $this->disconnectClient($client);
            return;
        }

        // do something with data
        foreach ($this->clients as $client) {
            $client->write($data);
        }
    }

    private function disconnectClient(Socket $client)
    {
        echo"Client disconnected";
    }

    private function createSocket(): Socket
    {
        $factory = new Factory();
        $socket = $factory->createServer('tcp://0.0.0.0:8080');
        $socket->setBlocking(false);

//        pcntl_signal(SIGTERM , function () use ($socket) {
//            echo "Socket shutdown\n";
//            $socket->shutdown();
//            $socket->close();
//        });
//        pcntl_signal(SIGINT , function () use ($socket) {
//            echo "Socket shutdown\n";
//            $socket->shutdown();
//            $socket->close();
//        });

        echo "Socket created\n";

        return $socket;
    }

    private function doHandshake(Socket $client, string $host_name, int $port)
    {
        if ($client->selectRead(0)) {
            echo "Executing handshake for ".$client->getPeerName()."\n";
            $received_header = $client->read(1024);
            $headers = array();
            $lines = preg_split("/\r\n/", $received_header);
            foreach ($lines as $line) {
                $line = chop($line);
                if (preg_match('/\A(\S+): (.*)\z/', $line, $matches)) {
                    $headers[$matches[1]] = $matches[2];
                }
            }

            if (!isset($headers['Sec-WebSocket-Key'])) {
                return;
            }

            $secKey = $headers['Sec-WebSocket-Key'];
            $secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
            $buffer = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
                "Upgrade: websocket\r\n" .
                "Connection: Upgrade\r\n" .
                "WebSocket-Origin: $host_name\r\n" .
                "WebSocket-Location: ws://$host_name:$port/\r\n" .
                "Sec-WebSocket-Accept:$secAccept\r\n\r\n";

            $client->write($buffer);
        }
    }
}

<?php

namespace App\Websocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface
{
    protected $clients;

    public function __construct()
    {
        $this->clients = [];
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Almacena la nueva conexión
        $this->clients[$conn->resourceId] = $conn;
        echo "Nueva conexión ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        echo "Mensaje recibido de {$from->resourceId}: $msg\n";
        foreach ($this->clients as $client) {
            // Enviar el mensaje a todos los clientes
            $client->send($msg);
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // Elimina la conexión
        unset($this->clients[$conn->resourceId]);
        echo "Conexión {$conn->resourceId} cerrada\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }
}

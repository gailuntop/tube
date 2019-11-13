<?php

namespace Ryp\Tube\Socket;

trait RypSocketClient
{
    use SocketConfig;
    
    public function rypSocketPush($to_user, $name, $content)
    {
        $client = new WebSocketClient($this->getConfig(), 9502);
        $client->connect();
        return $client->send(json_encode([
            'type' => 'send',
            'to_user' => $to_user,
            'content' => json_encode($content),
            'name' => $name
        ]));
    }
}



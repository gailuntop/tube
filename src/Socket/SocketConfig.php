<?php

namespace Ryp\Tube\Socket;

trait SocketConfig
{
    public function getConfig()
    {
        $config = [
            'local' => '192.168.1.188',
            'testing' => '192.168.1.188',
            'aliyun_test' => '119.23.203.248',
            'aliyun_test2' => '120.77.39.123',
            'production' => '47.106.47.19',
        ];
        return $config[env('APP_ENV')];
    }
}


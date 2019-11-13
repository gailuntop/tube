<?php

namespace Ryp\Tube\Push\Ali;

trait AliConfig{
    
    public function config()
    {
        if (env('APP_ENV') == 'aliyun_test') {
            $push = [
                1 => [
                    'appKeyAndroid' => 25035489,
                    'appKeyIos' => 25050154,
                ],
                2 => [
                    'appKeyAndroid' => 25369434,
                    'appKeyIos' => 25395425,
                ],
                4 => [
                    'appKeyAndroid' => 25025871,
                    'appKeyIos' => 24980855,
                ],
            ];
        } elseif (env('APP_ENV') == 'aliyun_test2') {
            $push = [
                1 => [
                    'appKeyAndroid' => 24819354,
                    'appKeyIos' => 25050154,
                ],
                2 => [
                    'appKeyAndroid' => 25369434,
                    'appKeyIos' => 25395425,
                ],
                4 => [
                    'appKeyAndroid' => 25025871,
                    'appKeyIos' => 24980855,
                ],
            ];
        } elseif (env('APP_ENV') == 'gray') {
            $push = [
                1 => [
                    'appKeyAndroid' => 27835699,
                    'appKeyIos' => 25050154,
                ],
                2 => [
                    'appKeyAndroid' => 27835698,
                    'appKeyIos' => 25395425,
                ],
                4 => [
                    'appKeyAndroid' => 27854408,
                    'appKeyIos' => 24980855,
                ],
            ];
        } else {
            $push = [
                1 => [
                    'appKeyAndroid' => 25264551, //24779310
                    'appKeyIos' => 25050154,
                ],
                2 => [
                    'appKeyAndroid' => 25448832,
                    'appKeyIos' => 25395425,
                ],
                4 => [
                    'appKeyAndroid' => 24981224,
                    'appKeyIos' => 24980855,
                ],
            ];
        }
        return [
            //app类型，1：供应商App、2：服务商App、3：车主App、4：vin查询App。
            'app' => [
                'accessKeyId' => 'LTAIJ26eP1PjRFoa',
                'accessKeySecret' => 'Tq82UL27QCinjWm7x9NiEGn6KUM41Y',
                'push' => [
                    '1' => [
                        'appKeyAndroid' => $push[1]['appKeyAndroid'] ?? '',
                        'appKeyIos' => $push[1]['appKeyIos'] ?? '',
                    ],
                    '2' => [
                        'appKeyAndroid' => $push[2]['appKeyAndroid'] ?? '',
                        'appKeyIos' => $push[2]['appKeyIos'] ?? '',
                    ],
                    '3' => '',
                    '4' => [
                        'appKeyAndroid' => $push[4]['appKeyAndroid'] ?? '',
                        'appKeyIos' => $push[4]['appKeyIos'] ?? '',
                    ],
                ]
            ],
        ];        
    }
}



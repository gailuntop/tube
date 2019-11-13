<?php

namespace Ryp\Tube\Push\Ali;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ryp\Tube\Push\Ali\Models\UserMsgSetting;

trait AppPush
{
    use AliConfig, AliPush;

    //app类型，1：供应商App、2：服务商App、3：车主App、4：vin查询App。
    public function pushBox($data)
    {
        $push = $this->config()['app']['push'];
        if ($data['target'] == 'ALIAS') {
            if ($re = UserMsgSetting::where([
                'identity_id' => $data['targetValue'],
                'app_type' => $data['app_type'],
            ])->first()) {
                if (!$re->receive_push_msg) {
                    unset($push[$data['app_type']]);
                }
            }
        }
        if (!empty($data['app_type'])) {
            $this->pushAction($data, $push[$data['app_type']]['appKeyAndroid'], $push[$data['app_type']]['appKeyIos']);
        } else {
            foreach ($push as $k => $v) {
                if ($v) {
                    $this->pushAction($data, $v['appKeyAndroid'], $v['appKeyIos']);
                }
            }
        }

    }

    //推送动作
    public function pushAction($data, $az, $ios)
    {
        //安卓
        if ($az) {
            $data['RypAppKey'] = $az;
            $data['RypDeviceType'] = 'ANDROID';
            $res = $this->push($data);
            Log::info($res);
        }

        //ios
        if ($ios) {
            $data['RypAppKey'] = $ios;
            $data['RypDeviceType'] = 'iOS';
            $res = $this->push($data);
            Log::info($res);
        }


    }

    /**
     * app推送
     */
    public function appPush($data)
    {
        $this->pushBox($data);
    }

    /**
     * 添加系统消息
     */
    public function addSystemMessage($request)
    {
        if ( !empty($request->appPush)) {
            $app_type = $request->app_type ?? 1;
            $target = $request->val['identity_id'] ? 'ALIAS' : 'ALL';
            if ($app_type == 4) { //vin app
                $target = 'ACCOUNT';
            }
            //app推送
            $this->pushBox([
                'target' => $target,
                'targetValue' => $request->val['identity_id'] ? $request->val['identity_id'] : 'ALL',
                'title' => $request->val['title'],
                'content' => $request->val['content'],
                'parameters' => json_encode($request->appPush),
                'app_type' => $app_type
            ]);
        }
    }
    
    
    
}

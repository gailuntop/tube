<?php

namespace Ryp\Tube\Token;

use Illuminate\Support\Facades\DB;
use Ryp\Utils\Http;
use Ryp\Utils\RypException;

trait RypCheckToken
{
    use RypCheckTokenData;

    public function rypToken($request)
    {
        $data = new \stdClass();
        $tokenData = $this->getTokenInfo($request->uid ?? '', $request->token ?? '');
        if (!$tokenData->generation_time) {
            throw new RypException('token invalid', 203);
        }
        //解析token
        $token = json_decode(base64_decode(explode('.', $tokenData->token)[0]), true);
        //微信用户
        if ($tokenData->identity_no == 1000) {
            return $this->getWx($token['user_id']);
        }
        //车主
        $mainData = $owner = (array)$this->identityInfo($token['identity_owner_id']);
        //车主详情
        $owner['user_info'] = (array)$this->getUserInfo($owner['identity_id']);
        $data->owner = $owner;
        //app_type和login_type
        $data->login_type = $tokenData->login_type;
        $data->app_type = $tokenData->app_type;
        //是否是服务商或者供应商
        if (in_array($tokenData->identity_no, [1002, 1003])) {
            $mainData = $seller = (array)$this->identityInfo($token['identity_company_id']);
            //公司信息
            $data->company = (array)$this->getCompany($seller['identity_id']);
            //员工信息
            $data->employee = (array)$this->getEmployee($seller['identity_id'], $owner['identity_id']);
        }
        return array_merge(collect($data)->toArray(), $mainData);
    }

    public function rypPower($request)
    {
        try{
            $token = json_decode(base64_decode(explode('.', $request->token)[0]));
            if (empty($token->user_id)) {
                throw new RypException('token invalid', 203);
            }
        } catch (RypException $e) {
            throw new RypException('token invalid', 203);
        }
        $request->module_code = $request->route()->getName(); //获得路由别名

        $power = new PowerCheck();
        //验证车主权限
        $power->verifyOwner($token->user_id, $token->identity_owner_id);
        if ($token->identity_company_id) {
            //验证公司权限
            $power->verifyCompany($token->identity_company_id, $token->identity_owner_id, $token->identity_no);
        }
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/11
 * Time: 12:00
 */

namespace Ryp\Tube\Token;

use Ryp\Utils\Http;
use Ryp\Utils\RypException;

class PowerCheck
{
    public $setTimeOut, $routeName;

    use RypCheckTokenDatabase;

    public function __construct()
    {
        $this->setTimeOut =  3600 * 24;
    }


    public function verify($request, $token)
    {
        if (empty($token->identity_owner_id)) {
            throw new RypException('请重新登录', 203);
        }
        $this->routeName = $request->module_code;
        $this->verifyOwner($token->user_id, $token->identity_owner_id);
        if ($token->identity_company_id) {
            $this->verifyCompany($token->identity_company_id, $token->identity_owner_id, $token->identity_no);
        }
    }

    //车主
    public function verifyOwner($user_id, $identity_id)
    {
        $key = 'permissions:1001:'.$identity_id;
        $this->createOwnerRole($key, $identity_id, $user_id);
        $value = json_decode(Http::RypRedis()->get($key), true);
        $this->doUrlRole($value);
    }

    //生成车主权限
    public function createOwnerRole($key, $identity_id, $user_id)
    {
        if (!Http::RypRedis()->exists($key)) {
            $value = [];
            $value = $this->doArray($this->identityPermissions($identity_id), $this->doArray($this->userPermissions($user_id), $value));
            $identityOther = $this->findData('user_identity_info', [
                ['user_id', '=', $user_id],
                ['identity_no', '>', 1001]
            ]);
            if ($identityOther->status ?? false) {
                $value = $this->doArray($this->identityPermissions($identityOther->id, $identityOther->identity_no), $value);
            }
//            \Log::INFO($value);
            Http::RypRedis()->set($key, json_encode($value));
            Http::RypRedis()->expire($key, $this->setTimeOut);
        }
    }

    //供应商
    public function verifyCompany($identity_company_id, $identity_owner_id, $identity_no = 1003)
    {
        $key = 'permissions:'.$identity_no.':'.$identity_company_id;
        $this->createCompanyRole($key, $identity_owner_id, $identity_company_id, $identity_no);
        $value = json_decode(Http::RypRedis()->hGet($key, $identity_owner_id), true);
        //throw new \Exception(json_encode($value));
        $this->doUrlRole($value);
    }

    //生成商家权限
    public function createCompanyRole($key, $identity_owner_id, $identity_company_id, $identity_no)
    {
        if (!Http::RypRedis()->exists($key)) {
            Http::RypRedis()->hSet($key, $identity_owner_id, '');
            Http::RypRedis()->expire($key, $this->setTimeOut);
        }
        $value = [];
        if (!Http::RypRedis()->hGet($key, $identity_owner_id)) {
            //供应商正常状态权限
            $value = $this->doArray($this->identityPermissions($identity_company_id, $identity_no), $value);
            //员工正常状态权限
            $value = $this->doArray($this->identityPermissions($identity_owner_id, $identity_no), $value);
            //员工禁止权限
            $value = $this->getEmployeeRole($identity_owner_id, $value);
            Http::RypRedis()->hSet($key, $identity_owner_id, json_encode($value));
        }
    }

    //处理当前路由权限
    public function doUrlRole($role)
    {
        //\Log::INFO($this->routeName);
        
        if ($re = $this->findData('route', ['route_code' => $this->routeName])) {
            if (!empty($role)) {
                if (!empty($role['forbids'])){
                    foreach ($role['forbids'] as $k => $v) {
                        if (in_array($re->id, explode(',', $v))) {
                            throw new RypException($k, 401);
                        }
                    }
                }
                $i = 0;
                if (!empty($role['grants'])){
                    foreach ($role['grants'] as $k => $v) {
                        if (in_array($re->id, explode(',', $v))) {
                            $i++;
                            break;
                        }
                    }
                }
                if (!$i) {
                    //throw new \Exception('你没有该权限', 401);
                }
            }
        }
        //throw new \Exception('你没有该权限,请联系管理员.', 401);
    }

    //处理数组
    public function doArray($data, $value)
    {
        if (empty($data)) {
            return [];
        }
        foreach ($data as $k => $v) {
            if ($v->type) { //允许
                $value['grants'][$v->name] = $v->acl;
            } else {
                $value['forbids'][$v->name] = $v->acl;
            }
        }
        return $value;
    }

    //员工权限获取
    public function getEmployeeRole($id, $value)
    {
        
        $re = $this->findData('user_employee', ['employee_identity_id' => $id]);
        $user_position = $this->findData('user_position', ['id' => $re->position_id]);
        $acl = $user_position->acl;
        if ($re->is_manage) { //特殊处理管理员admin权限
            $acl = reset($value['grants']);
        }
        if (is_array(json_decode($acl, true))) {
            $acl = implode(',', json_decode($acl, true));
        }
        $acl = implode(',', array_diff(explode(',', next($value['grants'])), explode(',', $acl)));
        $value['forbids']['禁止员工权限'] = $acl;
        return $value;
    }

    //账号权限查找
    public function userPermissions($id)
    {
        $this->firstOrCreate('user_permissions', [
            'user_id' => $id,
            'permissions_id' => 1,
            'permissions_code' => 'account',
        ]);
        return $this->joinData('user_permissions as up', 'permissions_config as pc', 'up.permissions_id', 'pc.id', [
            'up.user_id' => $id
        ]);

    }

    //身份权限查找
    public function identityPermissions($id, $identity_no = 1001)
    {
        if ($identity_no == 1001) {

            $this->firstOrCreate('identity_permissions', [
                'identity_id' => $id,
                'permissions_id' => 3,
                'permissions_code' => 'owner',
            ]);

            $this->firstOrCreate('identity_permissions', [
                'identity_id' => $id,
                'permissions_id' => 13,
                'permissions_code' => 'supplier_employee',
            ]);
        } else {
            $this->firstOrCreate('identity_permissions', [
                'identity_id' => $id,
                'permissions_id' => 5,
                'permissions_code' => 'supplier',
            ]);
        }
        return $this->joinData('identity_permissions as ip', 'permissions_config as pc', 'ip.permissions_id', 'pc.id', [
            'pc.identity_no' => $identity_no,
            'ip.identity_id' => $id
        ]);
    }

    // 不存在旧创建
    public function firstOrCreate($table, $data)
    {
        if (!$this->findData($table, $data)) {
            $this->addData($table, $data);
        }
    }

}
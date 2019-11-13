<?php

namespace Ryp\Tube\Token;

use Illuminate\Support\Facades\DB;
use Ryp\Utils\RypException;

trait RypCheckTokenData
{
    use RypCheckTokenDatabase;
    
    public function getTokenInfo($uid, $token)
    {
        if (!$res = $this->findData('user_tokens', [
            'uid' => $uid,
            'token' => $token
        ])) {
            throw new RypException('uid or token error', 203);
        }
        return $res;
    }
    
    public function identityInfo($id)
    {
        if (!$res = $this->findData('user_identity_info', [
            'id' => $id,
        ])) {
            throw new RypException('identity info not found', 203);
        }
        $res = collect($res)->except(['updated_at', 'deleted_at']);
        $res['identity_id'] = $res['id'];
        unset($res['id']);
        return (object)$res->toArray();
    }
    
    public function getUserInfo($identity_id)
    {
        if (!$res = $this->findData('user_info', [
            'identity_id' => $identity_id
        ])) {
            throw new RypException('user info not found', 203);
        }
        return (object)collect($res)->except(['id', 'updated_at', 'deleted_at'])->toArray();
    }

    public function getCompany($identity_id)
    {
        if (!$res = $this->findData('user_company', [
            'identity_id' => $identity_id
        ])) {
            throw new RypException('company info not found', 203);
        }
        return (object)collect($res)->except(['updated_at', 'deleted_at'])->toArray();
    }

    public function getEmployee($company_identity_id, $employee_identity_id)
    {
        if (!$res = $this->findData('user_employee', [
            'company_identity_id' => $company_identity_id,
            'employee_identity_id' => $employee_identity_id,
            'deleted_at' => null
        ])) {
            throw new RypException('company info not found', 203);
        }
        return (object)collect($res)->except(['updated_at', 'deleted_at'])->toArray();
    }
    
}
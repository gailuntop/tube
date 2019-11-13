<?php

namespace Ryp\Tube\Activity\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserIdentityInfo extends Model
{
    use SoftDeletes;
    protected $table = 'user.user_identity_info';
    protected $primaryKey = 'id';

    public function userToken()
    {
        return $this->hasMany('Ryp\Tube\Activity\Models\User\UserToken', 'user_id', 'user_id');
    }
    
}

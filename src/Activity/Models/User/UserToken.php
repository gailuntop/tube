<?php

namespace Ryp\Tube\Activity\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserToken extends Model
{
    use SoftDeletes;
    protected $table = 'user.user_tokens';
    protected $primaryKey = 'id';
    

}

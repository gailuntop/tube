<?php

namespace Ryp\Tube\Push\Ali\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserMsgSetting extends Model
{
    use SoftDeletes;
    protected $table = 'aliyun_message.user_msg_setting';
    protected $guarded = ['id'];
}

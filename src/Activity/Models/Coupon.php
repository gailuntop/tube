<?php

namespace Ryp\Tube\Activity\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use SoftDeletes;
    protected $table = 'marketing.coupon';
    protected $primaryKey = 'id';

    public function device()
    {
        return $this->belongsToMany('App\Model\Marketing\Device', 'coupon_to_device', 'coupon_id', 'device_id');
    }

    public function goodsScope()
    {
        return $this->belongsToMany('App\Model\Marketing\GoodsScope', 'coupon_to_goods_scope', 'coupon_id', 'goods_scope_id');
    }

}

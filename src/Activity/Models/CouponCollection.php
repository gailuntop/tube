<?php

namespace Ryp\Tube\Activity\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CouponCollection extends Model
{
    use SoftDeletes;
    protected $table = 'marketing.coupon_collection';
    protected $primaryKey = 'id';

    public function coupon()
    {
        return $this->belongsTo('Ryp\Tube\Activity\Models\Coupon', 'coupon_id');
    }
    

}

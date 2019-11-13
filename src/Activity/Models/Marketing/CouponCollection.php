<?php

namespace Ryp\Tube\Activity\Models\Marketing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CouponCollection extends Model
{
    use SoftDeletes;
    protected $table = 'marketing.coupon_collection';
    protected $primaryKey = 'id';

    public function coupon()
    {
        return $this->belongsTo('Ryp\Tube\Activity\Models\Marketing\Coupon', 'coupon_id');
    }

    public function couponReferrer()
    {
        return $this->hasOne('Ryp\Tube\Activity\Models\Marketing\CouponReferrer', 'coupon_collection_id', 'id');
    }

    public function promotionsActivity()
    {
        return $this->belongsTo('Ryp\Tube\Activity\Models\Marketing\PromotionsActivity', 'activity_id');
    }
    

}

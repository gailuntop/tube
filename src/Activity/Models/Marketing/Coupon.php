<?php

namespace Ryp\Tube\Activity\Models\Marketing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use SoftDeletes;
    protected $table = 'marketing.coupon';
    protected $primaryKey = 'id';

}

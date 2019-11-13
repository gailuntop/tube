<?php

namespace Ryp\Tube\Activity\Models\Marketing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PromotionsActivity extends Model
{
    use SoftDeletes;
    protected $table = 'marketing.promotions_activity';
    protected $primaryKey = 'id';

    public function activityType()
    {
        return $this->belongsTo('Ryp\Tube\Activity\Models\Marketing\ActivityType', 'activity_type_id');
    }

}

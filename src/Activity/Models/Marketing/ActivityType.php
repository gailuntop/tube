<?php

namespace Ryp\Tube\Activity\Models\Marketing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityType extends Model
{
    use SoftDeletes;
    protected $table = 'marketing.activity_type';
    protected $primaryKey = 'id';

}

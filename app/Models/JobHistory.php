<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobHistory extends Model
{
    

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'user_id',
        'position',
        'company_name',
        'department',
        'local',
        'pay',
        'job_part',
        'satrt_date',
        'end_date',
        'period_year',
        'period_mon',
        'created_at',
        'updated_at',
    ];
}

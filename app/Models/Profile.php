<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'user_id',
        'profile_img',
        'academy_type',
        'academy_local',
        'academy_name',
        'academy_major',
        'academy_time',
        'introduction',
        'apply_motive',
        'created_at',
        'updated_at',
    ];
}

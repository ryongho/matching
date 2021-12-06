<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplyInfo extends Model
{
    

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'user_id',
        'addr1',
        'addr2',
        'birthday',
        'gender',
        'career_type',
        'last_position',
        'interrest',
        'condition',
        'min_pay',
        'created_at',
        'updated_at',
    ];
}

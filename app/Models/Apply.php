<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apply extends Model
{
    

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'user_id',
        'company_id',
        'apply_code',
        'phone',
        'comment',
        'status',
        'type',
        'created_at',
        'updated_at',
    ];
}

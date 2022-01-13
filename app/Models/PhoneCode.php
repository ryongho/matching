<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhoneCode extends Model
{
    

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'phone',
        'code',
        'created_at',
    ];
}

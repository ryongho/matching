<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailCode extends Model
{
    

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'email',
        'code',
        'created_at',
    ];
}

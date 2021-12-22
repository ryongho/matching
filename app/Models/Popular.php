<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Popular extends Model
{
    

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'goods_id',
        'order_no',
        'created_at',
    ];
}

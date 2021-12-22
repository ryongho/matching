<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'title',
        'content',
        'writer',
        'img_src',
        'file_src',
        'start_date',
        'end_date',
        'created_at',
    ];
}

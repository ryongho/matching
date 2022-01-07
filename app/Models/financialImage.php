<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
 
class FinancialImage extends Model
{
    

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'user_id',
        'file_name',
        'doc_name',
        'order_no',
        'created_at'
    ];
}

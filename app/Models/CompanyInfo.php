<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyInfo extends Model
{
    

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'user_id',
        'company_name',
        'addr1',
        'addr2',
        'biz_item',
        'biz_type',
        'reg_no',
        'job_type',
        'introduction',
        'members',
        'type',
        'pay',
        'com_size',
        'condition',
        'investment',
        'sales',
        'profit',
        'created_at',
        'updated_at',
    ];
}

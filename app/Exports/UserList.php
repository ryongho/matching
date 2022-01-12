<?php

namespace App\Exports;

use App\Models\User;
use App\Models\ApplyInfo;
use App\Models\Profile;
use App\Models\JobHistory;
use App\Models\CompanyInfo;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class UserList implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    
    public function __construct($query)
    {

        $this->query = $query;
        //$this->start_date = $start_date;
        //$this->end_date = $end_date;
        //$this->keyword = $keyword;

    }


    public function collection()
    {
        return collect($this->query); 
    }

    

    public function headings(): array{

        $head = [
        '회원아이디',
        '회원휴대폰',
        '회원명',
        '성별',
        ];
        return $head;

    }


}

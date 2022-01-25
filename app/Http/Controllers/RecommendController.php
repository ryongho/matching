<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Recommend;
use App\Models\CompanyImage;
use App\Models\Goods;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class RecommendController extends Controller
{
    public function regist(Request $request)
    {
        
        $return = new \stdClass;        

        $recommend = Recommend::where('order_no',$request->order_no)->count();

        if($recommend){
            Recommend::where('order_no',$request->order_no)->delete();
        }
    
        $result = Recommend::insertGetId([
            'comapny_id'=> $request->company_id ,
            'order_no'=> $request->order_no ,
            'created_at'=> Carbon::now(),
        ]);

        if($result){ //DB 입력 성공
            $return->status = "200";
            $return->msg = "success";
        }else{
            $return->status = "501";
            $return->msg = "fail";
        }
        

        echo(json_encode($return));
    }

    public function list(Request $request){


        $rows = Recommend::join('company_infos', 'recommends.company_id', '=', 'company_infos.id')
                        ->select('company_infos.id as company_id','logo_img','company_name','job_type') 
                        ->orderBy('order_no','asc')
                        ->get();

        $i=0;                
        foreach($rows as $row){
            $rows[$i]['comapny_images'] = CompanyImage::select('file_name')->where('company_id',$row->company_id)->orderby('order_no','asc')->get();
            $i++;    
        }

        $return = new \stdClass;

        $return->status = "200";
        $return->cnt = count($rows);
        $return->data = $rows ;

        echo(json_encode($return));

    }

    



}

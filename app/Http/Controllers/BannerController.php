<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Banner;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BannerController extends Controller
{
    public function regist(Request $request)
    {
        $return = new \stdClass;

        $result = Banner::insertGetId([
            'type'=> $request->banner_type ,
            'img_url'=> $request->img_url ,
            'order_no'=> $request->order_no,
            'link_url'=> $request->link_url,
            'display'=> 'Y' ,
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
        $type = $request->banner_type;

        $rows = Banner::select('img_url','link_url','order_no',)->where('type',$type)->where('display','Y')->orderBy('order_no','asc')->get();

        $return = new \stdClass;

        $return->status = "200";
        $return->cnt = count($rows);
        $return->data = $rows ;

        echo(json_encode($return));

    }

    public function update(Request $request)
    {        
        $return = new \stdClass;

        $id = $request->banner_id;
        $result = Banner::where('id',$id)->update([
            'order_no'=> $request->order_no ,
            'img_url'=> $request->img_url ,
            'link_url'=> $request->link_url ,
            'display'=> $request->display ,
        ]);

        if($result){
            $return->status = "200";
            $return->msg = "success";

        }else{
            $return->status = "500";
            $return->msg = "fail";
        }

        echo(json_encode($return));    

    }

    public function delete(Request $request)
    {
        $return = new \stdClass;        
    
        $id = $request->banner_id;
        $result = Banner::where('id',$id)->delete();

        if($result){
            $return->status = "200";
            $return->msg = "success";

        }else{
            $return->status = "500";
            $return->msg = "fail";
        }

        echo(json_encode($return));    

    }

    



}

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
            'title'=> $request->title,
            'memo'=> $request->memo,
            'writer'=> "관리자",
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

        $rows = Banner::select('id as banner_id','img_url','link_url','order_no',)->where('type',$type)->where('display','Y')->orderBy('order_no','asc')->get();

        $return = new \stdClass;

        $return->status = "200";
        $return->cnt = count($rows);
        $return->data = $rows ;

        echo(json_encode($return));

    }

    public function list_admin(Request $request){
  
        $start_date = $request->start_date;     
        $end_date = $request->end_date;
        $keyword = $request->keyword;
        
        $page_no = $request->page_no;
        $start_no = ($page_no - 1) * 30 ;

        $type = $request->type;

        $return = new \stdClass;
        
        $rows = Banner::select('id as banner_id','img_url','link_url','order_no','writer')
                    ->when($keyword, function ($query, $keyword) {
                        return $query->where('title', 'like', "%".$keyword."%");
                    })
                    ->when($type, function ($query, $type) {
                        return $query->where('type', $type);
                    })
                    ->whereBetween('created_at',[$start_date.' 00:00:00',$end_date.' 23:59:59']) 
                    ->where('id','>',$start_no) 
                    ->orderby('id','desc')
                    ->get();
    

        $return->status = "200";
        $return->cnt = count($rows);
        $return->data = $rows;

        return response()->json($return, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);

        
    }

    public function detail_admin(Request $request){
  
        $banner_id = $request->banner_id;     
        
        $return = new \stdClass;
        
         
        $rows = Banner::select('id as banner_id','img_url','link_url','order_no','writer','display')
                    ->where('id',$banner_id) 
                    ->first();


        $return->status = "200";
        $return->data = $rows;

        return response()->json($return, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);

        
    }

    public function update(Request $request)
    {        
        $return = new \stdClass;

        $id = $request->banner_id;
        $result = Banner::where('id',$id)->update([
            //'order_no'=> $request->order_no ,
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

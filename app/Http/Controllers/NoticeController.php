<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Notice;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class NoticeController extends Controller
{
    public function regist(Request $request)
    {
        
        $return = new \stdClass;   
        
        $result = Notice::insertGetId([
            'title'=> $request->title ,
            'content'=> $request->content ,
            'writer'=> "관리자" ,
            'img_src'=> $request->img_src ,
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


        $rows = Notice::select('id','title','created_at','content','img_src')->orderby('created_at','desc')->get();

        $return = new \stdClass;

        $return->status = "200";
        $return->cnt = count($rows);
        $return->data = $rows ;

        echo(json_encode($return));

    }

    public function detail(Request $request){
        $id = $request->id;

        $rows = Notice::where('id',$id)->select('id','title','created_at','content','img_src','created_at')->first();

        $return = new \stdClass;

        $return->status = "200";
        $return->data = $rows ;

        echo(json_encode($return));

    }

    public function list_admin(Request $request){
  
        $start_date = $request->start_date;     
        $end_date = $request->end_date;
        $keyword = $request->keyword;
        
        $page_no = $request->page_no;
        $start_no = ($page_no - 1) * 30 ;

        $return = new \stdClass;

        $login_user = Auth::user();
        $user_id = $login_user->getId();
        
        $rows = Notice::select('id as notice_id','title','created_at','content','writer')
                    ->when($keyword, function ($query, $keyword) {
                        return $query->where('title', 'like', "%".$keyword."%");
                    })
                    ->whereBetween('notices.created_at',[$start_date.' 00:00:00',$end_date.' 23:59:59']) 
                    ->where('notices.id','>',$start_no) 
                    ->orderby('notices.id','desc')
                    ->get();
    

        $return->status = "200";
        $return->cnt = count($rows);
        $return->data = $rows;

        return response()->json($return, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);

        
    }

    public function detail_admin(Request $request){
  
        $notice_id = $request->notice_id;     
        
        $return = new \stdClass;
        
         
        $rows = Notice::select('id as notice_id','title','created_at','content','img_src')
                    ->where('id',$notice_id) 
                    ->first();

        

        $return->status = "200";
        $return->data = $rows;

        return response()->json($return, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);

        
    }

    public function delete(Request $request)
    {
        $return = new \stdClass;        
    
        $ids = explode(',',$request->notice_id);
        $result = Notice::whereIn('id',$ids)->delete();

        if($result){
            $return->status = "200";
            $return->msg = "success";

        }else{
            $return->status = "500";
            $return->msg = "fail";
        }

        echo(json_encode($return));    

    }

    public function update(Request $request)
    {
        $return = new \stdClass;
        
        $notice_id = $request->notice_id;

        $result = Notice::where('id', $notice_id)->update([
            'title' => $request->title,
            'content' => $request->content,
            'img_src' => $request->img_src        
        ]);

        if($result){
            $return->status = "200";
            $return->msg = "success";
        }else{
            $return->status = "500";
            $return->msg = "fail";
        }
        
        return response()->json($return, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);

        
    }


}

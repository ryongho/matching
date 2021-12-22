<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Blog;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BlogController extends Controller
{
    public function regist(Request $request)
    {
        $return = new \stdClass;

        $result = Blog::insertGetId([
            'title'=> $request->title ,
            'content'=> $request->content ,
            'writer'=> $request->writer ,
            'img_src'=> $request->img_src ,
            'file_src'=> $request->file_src ,
            'start_date'=> $request->start_date ,
            'end_date'=> $request->end_date ,
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

    public function list(Request $request){ // 메인페이지용 리스트
        $start_no = $request->start_no;
        $offset = $request->offset;

        $todate = Carbon::now();

        $rows = Blog::select('id as blog_id','title','img_src')
                ->where('start_date' ,'<=',$todate)->where('end_date','>=',$todate)->orderBy('id','desc')
                ->where('id','>=', $start_no)
                ->limit($offset)
                ->get();

        $return = new \stdClass;

        $return->status = "200";
        $return->cnt = count($rows);
        $return->data = $rows ;

        echo(json_encode($return));

    }

    public function update(Request $request)
    {        
        $return = new \stdClass;

        $id = $request->blog_id;

        $result = Blog::where('id',$id)->update([
            'title'=> $request->title ,
            'content'=> $request->content ,
            'img_src'=> $request->img_src ,
            'file_src'=> $request->file_src ,
            'start_date'=> $request->start_date ,
            'end_date'=> $request->end_date ,
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
    
        $id = $request->blog_id;
        $result = Blog::where('id',$id)->delete();

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

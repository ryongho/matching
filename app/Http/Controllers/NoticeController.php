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


}

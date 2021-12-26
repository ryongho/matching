<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Qna;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QnaController extends Controller
{
    

    public function regist(Request $request)
    {
        //dd($request);
        $return = new \stdClass;

        $login_user = Auth::user();
        $user_id = $login_user->getId();

        
        Qna::insert([
            'user_id'=> $user_id ,
            'type'=> $request->type,
            'email'=> $request->email ,
            'title'=> $request->title ,
            'content'=> $request->content ,
            'status'=> "W" ,
            'created_at'=> Carbon::now(),
        ]);

        $return->status = "200";
        $return->added = 'Y';

        echo(json_encode($return));
        
    }

    public function answer(Request $request)
    {
        $return = new \stdClass;
        
        $qna_id = $request->qna_id;

        $result = Qna::where('id', $qna_id)->update([
            'answer' => $request->answer,
            'status'=> "S" ,                 
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

    public function list(){

        $return = new \stdClass;

        $login_user = Auth::user();
        $user_id = $login_user->getId();
         
        $rows = Qna::select('id as qna_id','title','content','type','status','created_at') 
                    ->orderby('created_at','desc')
                    ->get();


        $return->status = "200";
        $return->cnt = count($rows);
        $return->data = $rows;

        echo(json_encode($return));
        
    }



}

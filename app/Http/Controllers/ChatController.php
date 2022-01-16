<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function create(Request $request)
    {

        //$login_user = Auth::user();
        
        //$user_id = $login_user->user_id;
        $creator = $request->creator;
        $now = Carbon::now();
        $channel = "chat_".$creator."_".$request->guest."_".$now->format('YmdHis');
        $return = new \stdClass;

        $result = Chat::insertGetId([
            'creator'=> $creator ,
            'guest'=> $request->guest ,
            'channel'=> $channel,
            'created_at'=> Carbon::now(),
        ]);

        if($result){ //DB 입력 성공
            $return->status = "200";
            $return->msg = "success";
            $return->channel = $channel;
        }else{
            $return->status = "501";
            $return->msg = "fail";
        }
        

        echo(json_encode($return));
    }

    public function list(Request $request){

        $login_user = Auth::user();
        
        $user_id = $request->user_id;

        $rows = Chat::select('id as chat_id','channel','creator','guest')->where('creator',$user_id)->orWhere('guest',$user_id)->get();

        $return = new \stdClass;

        $return->status = "200";
        $return->cnt = count($rows);
        $return->data = $rows ;

        echo(json_encode($return));

    }

    



}

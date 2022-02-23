<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\JobHistory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class JobhistoryController extends Controller
{

    public function list(Request $request){
        $user_id = $request->user_id;

        $rows = JobHistory::where('user_id',$user_id)->orderBy('id','asc')->get();
        

        $return = new \stdClass;

        $return->status = "200";
        $return->cnt = count($rows);
        $return->data = $rows ;

        return response()->json($return, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);

    }

    

    



}

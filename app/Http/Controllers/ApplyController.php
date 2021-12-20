<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CompanyInfo;
use App\Models\ApplyInfo;
use App\Models\Apply;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApplyController extends Controller
{
    public function regist(Request $request)
    {

        $return = new \stdClass;

        //$login_user = Auth::user();
        //$user_id = $login_user->getId();
        $user_id = $request->user_id;
        $company_id = $request->company_id;
        $status = $request->status;

        $apply_info = Apply::where('company_id',$request->company_id)->where('user_id',$user_id)->first();

        if(isset($apply_info->id)){

            $result = Apply::where('id',$apply_info->id)->update([
                'status'=> $status 
            ]); 

        }else{

            $result = Apply::insertGetId([
                'user_id'=> $user_id ,
                'company_id'=> $company_id ,
                'status'=> $status ,
                'created_at'=> Carbon::now(),
            ]);      

        }

        
        if($result){ //DB 입력 성공
            $return->status = "200";
            $return->msg = "success";
        }else{
            $return->status = "501";
            $return->msg = "fail";
        }
    

        return response()->json($return, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);
        
    }


    public function list_by_user(Request $request){


        $rows = Apply::join('company_infos', 'applies.company_id', '=', 'company_infos.id')
                    ->select('applies.id as apply_id','company_infos.id as company_id','logo_img','company_name','job_type', 'status') 
                    ->where('applies.user_id',$request->user_id)
                    ->whereIn('status', ['A','R','W','SE','IW','I','L'])
                    ->orderby('applies.created_at','desc')
                    ->get();

        $return = new \stdClass; 

        $return->status = "200";
        $return->cnt = count($rows);
        $return->data = $rows ;

        echo(json_encode($return));

    }

    public function success_list_by_user(Request $request){


        $rows = Apply::join('company_infos', 'applies.company_id', '=', 'company_infos.id')
                    ->select('applies.id as apply_id','company_infos.id as company_id','logo_img','company_name','job_type', 'status') 
                    ->where('applies.user_id',$request->user_id)
                    ->whereIn('status', ['S'])
                    ->orderby('applies.created_at','desc')
                    ->get();

        $return = new \stdClass; 

        $return->status = "200";
        $return->cnt = count($rows);
        $return->data = $rows ;

        echo(json_encode($return));

    }

    public function cancel_list_by_user(Request $request){


        $rows = Apply::join('company_infos', 'applies.company_id', '=', 'company_infos.id')
                    ->select('applies.id as apply_id','company_infos.id as company_id','logo_img','company_name','job_type', 'status') 
                    ->where('applies.user_id',$request->user_id)
                    ->whereIn('status', ['WC','RJ','IC','LC'])
                    ->orderby('applies.created_at','desc')
                    ->get();

        $return = new \stdClass; 

        $return->status = "200";
        $return->cnt = count($rows);
        $return->data = $rows ;

        echo(json_encode($return));

    }

    public function detail(Request $request){
        
        $apply_id = $request->apply_id;

        $rows = Apply::join('company_infos', 'applies.company_id', '=', 'company_infos.id')
                    ->where('applies.id',$apply_id)
                    ->select('applies.id as apply_id','company_infos.company_name','logo_img','introduction','job_type', 'type','com_size','pay','condition','status' ) 
                    ->first();
        
        $return = new \stdClass;

        $return->status = "200";
        $return->data = $rows ;

        echo(json_encode($return));

    }


    public function list_by_company(Request $request){


        $rows = Apply::join('apply_infos', 'apply_infos.user_id', '=', 'applies.user_id')
                    ->join('users', 'users.id', '=', 'applies.user_id')
                    ->select('applies.id as apply_id','applies.user_id','profile_img','users.name','addr1','addr2', 'status') 
                    ->where('applies.company_id',$request->company_id)
                    ->whereIn('status', ['A','R','W','SE','IW','I','L'])
                    ->orderby('applies.created_at','desc')
                    ->get();

        $return = new \stdClass; 

        $return->status = "200";
        $return->cnt = count($rows);
        $return->data = $rows ;

        echo(json_encode($return));

    }

    public function success_list_by_company(Request $request){


        $rows = Apply::join('apply_infos', 'apply_infos.user_id', '=', 'applies.user_id')
                ->join('users', 'users.id', '=', 'applies.user_id')
                ->select('applies.id as apply_id','applies.user_id','profile_img','users.name','addr1','addr2', 'status') 
                ->where('applies.company_id',$request->company_id)
                ->whereIn('status', ['S'])
                ->orderby('applies.created_at','desc')
                ->get();            

        $return = new \stdClass; 

        $return->status = "200";
        $return->cnt = count($rows);
        $return->data = $rows ;

        echo(json_encode($return));

    }

    public function cancel_list_by_company(Request $request){


        $rows = Apply::join('apply_infos', 'apply_infos.user_id', '=', 'applies.user_id')
                ->join('users', 'users.id', '=', 'applies.user_id')
                ->select('applies.id as apply_id','applies.user_id','profile_img','users.name','addr1','addr2', 'status') 
                ->where('applies.company_id',$request->company_id)
                ->whereIn('status', ['WC','RJ','IC','LC'])
                ->orderby('applies.created_at','desc')
                ->get();  

                    

        $return = new \stdClass; 

        $return->status = "200";
        $return->cnt = count($rows);
        $return->data = $rows ;

        echo(json_encode($return));

    }

    public function detail_apply(Request $request){
        
        $apply_id = $request->apply_id;

        $rows = Apply::join('apply_infos', 'apply_infos.user_id', '=', 'applies.user_id')
                    ->join('users', 'users.id', '=', 'applies.user_id')
                    ->join('profiles', 'profiles.user_id', '=', 'applies.user_id')
                    ->where('applies.id',$apply_id)
                    ->select('applies.id as apply_id','applies.user_id','profiles.profile_img','users.name','addr1','addr2', 'status','gender','birthday','career_type','last_position','interest','condition','profiles.introduction' ) 
                    ->first();
        
        $return = new \stdClass;

        $return->status = "200";
        $return->data = $rows ;

        echo(json_encode($return));

    }



    



}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CompanyInfo;
use App\Models\ApplyInfo;
use App\Models\Apply;
use App\Models\Payment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApplyController extends Controller
{
    public function regist(Request $request)
    {

        $return = new \stdClass;

        $login_user = Auth::user();
        $req_id = $login_user->getId();
        $user_id = $request->user_id;

        $apply_type = "요청";
        if($req_id == $user_id){
            $apply_type = "신청";     
        }
        
        $company_id = $request->company_id;
        $status = $request->status;
        $comment = $request->comment;
        $phone = $request->phone;

        $apply_info = Apply::where('company_id',$request->company_id)->where('user_id',$user_id)->first();

        if(isset($apply_info->id)){

            $result = Apply::where('id',$apply_info->id)->update([
                'status'=> $status 
            ]); 

        }else{

            $dt = \Carbon\Carbon::now();

            $result = Apply::insertGetId([
                'user_id'=> $user_id ,
                'company_id'=> $company_id ,
                'status'=> $status ,
                'comment'=> $comment ,
                'phone'=> $phone ,
                'type'=> $apply_type ,
                'created_at'=> $dt,
            ]);
            

            if($result){
                $apply_code = "A".$dt->format('Ymd').$user_id.$company_id.$result;
                Apply::where('id',$result)->update([
                    'apply_code'=> $apply_code,
                ]); 
                $return->apply_code = $apply_code;
                $return->apply_time = $dt->format('Y-m-d H:i:s');
            }    

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

    public function list(Request $request){
        $type = $request->type;     
        $start_date = $request->start_date;     
        $end_date = $request->end_date;
        $keyword = $request->keyword;
        $status = $request->status;
        $apply_type = $request->apply_type;

        $page_no = $request->page_no;
        $start_no = ($page_no - 1) * 30 ;

        $rows = Apply::join('company_infos', 'applies.company_id', '=', 'company_infos.id')
                    ->join('users', 'users.id', '=', 'applies.user_id')
                    ->select('applies.id as apply_id',
                            'company_infos.id as company_id',
                            'users.name as name',
                            'users.email as email',
                            'applies.apply_code',
                            'company_infos.company_name as company_name',
                            'company_infos.job_type as job_type',
                            'applies.status as status',
                            'applies.created_at as created_at'
                            ) 
                    ->when($type, function ($query, $type) {
                        if($type == "전체"){
                            return;
                        }else{
                            return $query->where('company_infos.job_type', $type );
                        }
                        
                    })
                    ->when($keyword, function ($query, $keyword) {
                        return $query->where('company_name', 'like', "%".$keyword."%");
                    })
                    ->when($apply_type, function ($query, $apply_type) {
                        if($apply_type == "전체"){
                            return;
                        }else{
                            return $query->where('type', $apply_type);
                        }
                    })
                    ->when($status, function ($query, $status) {
                        if($status == "전체"){
                            return;
                        }else{
                            return $query->where('status', $status);
                        }
                    })
                    ->where('applies.id','>',$start_no) 
                    ->orderby('applies.created_at','desc')
                    ->get();

        $return = new \stdClass; 

        $return->status = "200";
        $return->cnt = count($rows);
        $return->data = $rows ;

        return response()->json($return, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);

    }

    public function detail_admin(Request $request){
        
        $apply_id = $request->apply_id;
        $return = new \stdClass;

        $rows = Apply::join('company_infos', 'applies.company_id', '=', 'company_infos.id')
                    ->join('apply_infos', 'apply_infos.user_id', '=', 'applies.user_id')
                    ->join('users', 'users.id', '=', 'applies.user_id')
                    ->join('profiles', 'profiles.user_id', '=', 'applies.user_id')
                    ->where('applies.id',$apply_id)
                    ->select('applies.id as apply_id',
                            'applies.user_id',
                            'company_infos.id as company_id',
                            'company_name',
                            'biz_item',
                            'biz_type',
                            'company_infos.type',
                            'company_infos.addr1',
                            'company_infos.addr2',
                            'members',
                            'apply_infos.condition',
                            'com_size',
                            'investment',
                            'sales',
                            'profit',
                            'pay',
                            'status',
                            'profiles.profile_img',
                            'users.email',
                            'apply_infos.addr1 as apply_addr1',
                            'apply_infos.addr2 as apply_addr2',
                            'status',
                            'profiles.profile_img',
                            'gender',
                            'birthday',
                            'comment',
                    ) 
                    ->first();

        if($rows){
            $payment = Payment::where('apply_id', $apply_id)
                    ->select('status','price') 
                    ->first();

            if($payment){
                $return->payment_status = $payment['status'] ;
                $return->payment_price = $payment['price'] ;
            }else{
                $return->payment_status = "미결제" ;
            }
        }
        
        

        $return->status = "200";
        $return->data = $rows ;

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

        return response()->json($return, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);

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
                    ->whereIn('status', ['WC','RJ','IC','LC','AC','IR'])
                    ->orderby('applies.created_at','desc')
                    ->get();

        $return = new \stdClass; 

        $return->status = "200";
        $return->cnt = count($rows);
        $return->data = $rows ;

        return response()->json($return, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);

    }

    public function detail(Request $request){
        
        $apply_id = $request->apply_id;

        $rows = Apply::join('company_infos', 'applies.company_id', '=', 'company_infos.id')
                    ->where('applies.id',$apply_id)
                    ->select('applies.id as apply_id','company_infos.company_name','logo_img','introduction','job_type', 'applies.type','com_size','pay','condition','status' ) 
                    ->first();
        
        $return = new \stdClass;

        $return->status = "200";
        $return->data = $rows ;

        return response()->json($return, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);

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

        return response()->json($return, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);

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

        return response()->json($return, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);

    }

    public function cancel_list_by_company(Request $request){


        $rows = Apply::join('apply_infos', 'apply_infos.user_id', '=', 'applies.user_id')
                ->join('users', 'users.id', '=', 'applies.user_id')
                ->select('applies.id as apply_id','applies.user_id','profile_img','users.name','addr1','addr2', 'status') 
                ->where('applies.company_id',$request->company_id)
                ->whereIn('status', ['WC','RJ','IC','LC','AC','IR'])
                ->orderby('applies.created_at','desc')
                ->get();  

                    

        $return = new \stdClass; 

        $return->status = "200";
        $return->cnt = count($rows);
        $return->data = $rows ;

        return response()->json($return, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);

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

        return response()->json($return, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);

    }



    



}

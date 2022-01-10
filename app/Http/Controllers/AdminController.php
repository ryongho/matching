<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function regist(Request $request)
    {
        //dd($request);
        $return = new \stdClass;

        $return->status = "500";
        $return->msg = "관리자에게 문의";

        $user_id = $request->user_id;
        /* 중복 체크 - start*/
        $email_cnt = User::where('email',$user_id)->count();

        if($email_cnt){
            $return->status = "602";
            $return->msg = "사용중인 아이디";
            $return->data = $request->user_id;
        }else{
            $result = User::insert([
                'name'=> $request->name ,
                'email' => $user_id,                 
                'user_type' => 3, // 관리자 
                'activity'=> $request->activity ,
                'memo'=> $request->memo ,
                'created_at' => Carbon::now(),
                'password' => Hash::make($request->password)
            ]);

            if($result){
                $return->status = "200";
                $return->msg = "관리자 등록 성공";
            }
        }    
        

        return response()->json($return, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);

        //return view('user.profile', ['user' => User::findOrFail($id)]);
    }

    

    public function login(Request $request){
        $user = User::where('email' , $request->user_id)->where('activity','Y')->first();

        $return = new \stdClass;

        if(!$user){
            $return->status = "501";
            $return->msg = "존재하지 않는 아이디 입니다.";
            $return->email = $request->email;
        }else if (Hash::check($request->password, $user->password)) {
            //echo("로그인 확인");
            Auth::loginUsingId($user->id);
            $login_user = Auth::user();

            $token = $login_user->createToken('user');

            $return->status = "200";
            $return->msg = "성공";
            //$return->dormant = $login_user->dormant;
            $return->token = $token->plainTextToken;
            $return->user_type = $login_user->user_type;

            User::where('email',$request->user_id)->update([
                'last_login' =>Carbon::now(),
                'last_ip' =>$request->getClientIp(),
            ]);
            
            //dd($token->plainTextToken);    
        }else{
            $return->status = "500";
            $return->msg = "아이디 또는 패스워드가 일치하지 않습니다.";
            $return->email = $request->email;
        }

        return response()->json($return, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);
    }

    public function logout(Request $request){
        $user_info = Auth::user();
        $user = User::where('id', $user_info->id)->first();
        $user->tokens()->delete();

        $return = new \stdClass;
        $return->status = "200";
        $return->msg = "success";

        return response()->json($return, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);
    }
    
    public function list(Request $request){
        $page_no = $request->page_no;

        $start_no = ($page_no - 1) * 30 ;
        $rows = User::select('id','activity','user_type','email as user_id','name','created_at','last_login','last_ip')
        ->whereIn('user_type',['3','4'])
        ->where('id','>',$start_no)
        ->orderBy('id', 'desc')
        ->limit(30)
        ->get();

        $cnt = User::whereIn('user_type',['3','4'])->count();

        $list = new \stdClass;

        $list->status = "200";
        $list->msg = "success";
        $list->total = $cnt;
        $list->data = $rows;
        
        return response()->json($list, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);
        
    }

    public function profile_detail(Request $request){

        $list = new \stdClass;

        $rows = User::join('apply_infos', 'apply_infos.user_id', '=', 'users.id')
                    ->join('profiles', 'profiles.user_id', '=', 'users.id')
                    ->select(
                        'profiles.id as profile_id',
                        'users.id as user_id',
                        'users.name as name',
                        'apply_infos.gender as gender',
                        'apply_infos.addr1 as addr1',
                        'apply_infos.addr2 as addr2',
                        'apply_infos.birthday as birthday',
                        'apply_infos.career_type as career_type',
                        'apply_infos.last_position as last_position',
                        'apply_infos.interest as interest',
                        'apply_infos.min_pay as min_pay',
                        'apply_infos.condition as condition',
                        'apply_infos.profile_img as profile_img',
                        'profiles.academy_type as academy_type',
                        'profiles.academy_local as academy_local',
                        'profiles.academy_name as academy_name',
                        'profiles.academy_major as academy_major',
                        'profiles.academy_time as academy_time',
                        'profiles.introduction as introduction',
                        'profiles.apply_motive as apply_motive',
                        'profiles.addr as addr',
                    )
                    ->where('user_type','0')
                    ->where('users.id', $request->user_id)
                    ->first();
        if($rows){

            //나이 계산 - start
            $bs = explode("-",$rows->birthday);
            $bs[0];
            $dt = Carbon::now();
            $to_year = $dt->format('Y');
            $rows->age = $to_year - $bs[0] +1;
            //나이 계산 - end

            
            $rows_history =Jobhistory::where('user_id',$request->user_id)
                        ->select(
                            'user_id as user_id',
                            'id as jobhistory_id',
                            'position as position',
                            'local as local',
                            'company_name as company_name',
                            'department as department',
                            'pay as pay',
                            'job_part as job_part',
                            'start_date as start_date',
                            'end_date as end_date',
                            'period_year as period_year',
                            'period_mon as period_mon'
                        )
                        ->get();
        
            $rows->jobhistories = $rows_history;

            $list->status = "200";
            $list->msg = "success";
            $list->data = $rows;
        }else{
            $list->status = "500";
            $list->msg = "해당 정보가 없습니다.";
        }
        
        
        return response()->json($list, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);
        
    }

    

    public function update(Request $request){
        //dd($request);
        $return = new \stdClass;


        $login_user = Auth::user();

        $return->status = "200";
        $return->msg = "변경 완료";
        $return->key = $request->key;
        $return->value = $request->value;

        $key = $request->key;
        $value = $request->value;
        $user_id = $login_user->id;

        if($key == "password"){
            $value = Hash::make($request->value);
        }

        $result = User::where('id', $user_id)->update([$key => $value]);

        if(!$result){
            $return->status = "500";
            $return->msg = "변경 실패";
        }

        return response()->json($return, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);

    }

    


    

    


}

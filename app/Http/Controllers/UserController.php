<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ApplyInfo;
use App\Models\Profile;
use App\Models\JobHistory;
use App\Models\EmailCode;
use App\Models\CompanyImage;
use App\Models\FinancialImage;
use App\Models\CompanyInfo;
use App\Models\Popular;
use App\Models\SearchKeyword;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function regist(Request $request)
    {
        //dd($request);
        $return = new \stdClass;

        $return->status = "500";
        $return->msg = "관리자에게 문의";
        $return->data = $request->user_id;

        /* 중복 체크 - start*/
        $email_cnt = User::where('email',$request->email)->count();
        $phone_cnt = User::where('phone',$request->phone)->count();

        if($email_cnt){
            $return->status = "602";
            $return->msg = "사용중인 이메일";
            $return->data = $request->email;
        }else if ($phone_cnt){
            $return->status = "603";
            $return->msg = "사용중인 폰 번호";
            $return->data = $request->phone;
        //중복 체크 - end
        }else{
            $user_id = User::insertGetId([
                'name'=> $request->name ,
                'email' => $request->email,                 
                'phone' => $request->phone, 
                'user_type' => 0,
                'push' => 'Y',
                'push_event' => 'Y',
                'created_at' => Carbon::now(),
                'password' => Hash::make($request->password)
            ]);

            if($user_id){
                $result = ApplyInfo::insertGetId([
                    'user_id'=> $user_id ,
                    'addr1' => $request->addr1,                 
                    'addr2' => $request->addr2,
                    'birthday' => $request->birthday,
                    'gender' => $request->gender,
                    'career_type' => $request->career_type,
                    'last_position' => $request->last_position,
                    'interest' => $request->interest,
                    'condition' => $request->condition,
                    'min_pay' => $request->min_pay, 
                    'profile_img' => $request->profile_img, 
                    'created_at' => Carbon::now()
                ]);

                if($result){

                    Auth::loginUsingId($user_id);
                    $login_user = Auth::user();
    
                    $token = $login_user->createToken('user');
    
                    $return->status = "200";
                    $return->msg = "success";
                    $return->data = $request->name;
                    $return->token = $token->plainTextToken;
                }

                
            }else{
                $return->status = "500";
                $return->msg = "회원등록 실패";
            }

            
            
            
        }
        

        return response()->json($return, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);

        //return view('user.profile', ['user' => User::findOrFail($id)]);
    }

    public function regist_company(Request $request)
    {
        //dd($request);
        $return = new \stdClass;

        $return->status = "500";
        $return->msg = "관리자에게 문의";
        $return->data = $request->user_id;

        /* 중복 체크 - start*/
        $email_cnt = User::where('email',$request->email)->count();
        $phone_cnt = User::where('phone',$request->phone)->count();
        
        if($email_cnt){
            $return->status = "602";
            $return->msg = "사용중인 이메일";
            $return->data = $request->email;
        }else if ($phone_cnt){
            $return->status = "603";
            $return->msg = "사용중인 폰 번호";
            $return->data = $request->phone;
        //중복 체크 - end
        }else{
            $user_id = User::insertGetId([
                'name'=> $request->name ,
                'email' => $request->email,                 
                'phone' => $request->phone, 
                'user_type' => 1,
                'push' => 'Y',
                'push_event' => 'Y',
                'created_at' => Carbon::now(),
                'password' => Hash::make($request->password)
            ]);

            if($user_id){
                $result = CompanyInfo::insertGetId([
                    'user_id'=> $user_id ,
                    'company_name' => $request->company_name,                 
                    'biz_item' => $request->biz_item,
                    'biz_type' => $request->biz_type,
                    'reg_no' => $request->reg_no,
                    'job_type' => $request->job_type,
                    'history' => $request->history,
                    'addr1' => $request->addr1,
                    'addr2' => $request->addr2,
                    'introduction' => $request->introduction, 
                    'members' => $request->members, 
                    'type' => $request->type, 
                    'com_size' => $request->com_size, 
                    'pay' => $request->pay, 
                    'condition' => $request->condition, 
                    'investment' => $request->investment, 
                    'sales' => $request->sales, 
                    'profit' => $request->profit, 
                    'logo_img' => $request->logo_img, 
                    'created_at' => Carbon::now()
                ]);

                if($result){ //DB 입력 성공

                    $no = 1; 
    
                    $cimages = explode(",",$request->com_img);
                    foreach( $cimages as $cimage){
                    
                        $result_img = CompanyImage::insertGetId([
                            'company_id'=> $result ,
                            'file_name'=> $cimage ,
                            'order_no'=> $no ,
                            'created_at' => Carbon::now()
                        ]);
    
                        $no++;
                    }
                    
                    $no2 = 1; 
    
                    $fimages = explode(",",$request->financial_img);

                    $doc_names = explode(",",$request->doc_names);
    
                    foreach( $fimages as $fimage){
                        
                        $result_img = FinancialImage::insertGetId([
                            'company_id'=> $result ,
                            'file_name'=> $fimage ,
                            'order_no'=> $no2 ,
                            'doc_name'=> $doc_names[$no2-1] ,
                            'created_at' => Carbon::now()
                        ]);
    
                        $no2++;
                    }

     
    
    


                    Auth::loginUsingId($user_id);
                    $login_user = Auth::user();
    
                    $token = $login_user->createToken('user');
    
                    $return->status = "200";
                    $return->msg = "success";
                    $return->data = $request->name;
                    $return->token = $token->plainTextToken;

                }


                
            }else{
                $return->status = "500";
                $return->msg = "회원등록 실패";
            }

            
            
            
        }
        

        return response()->json($return, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);

        //return view('user.profile', ['user' => User::findOrFail($id)]);
    }

    public function regist_profile(Request $request){
        
        $return = new \stdClass;
        
        $login_user = Auth::user();
        $user_id = $login_user->getId();
        
            $profile_id = Profile::insertGetId([
                'user_id'=> $user_id ,
                'addr'=> $request->addr ,
                'profile_img' => $request->profile_img,                 
                'academy_type' => $request->academy_type, 
                'academy_local' => $request->academy_local, 
                'academy_name' => $request->academy_name, 
                'academy_major' => $request->academy_major, 
                'academy_time' => $request->academy_time, 
                'introduction' => $request->introduction, 
                'apply_motive' => $request->apply_motive, 
                'created_at' => Carbon::now()
            ]);

            /*if($profile_id){
                $result = JobHistory::insertGetId([
                    'profile_id'=> $profile_id ,
                    'company_name' => $request->company_name,                 
                    'department' => $request->department,
                    'local' => $request->local,
                    'pay' => $request->pay,
                    'job_part' => $request->job_part,
                    'satrt_date' => $request->satrt_date,
                    'end_date' => $request->end_date,
                    'period_year' => $request->period_year,
                    'period_mon' => $request->period_mon, 
                    'created_at' => Carbon::now()
                ]);

                if($result){ //DB 입력 성공

                
                    $return->status = "200";
                    $return->msg = "success";
                
                }*/

            if($profile_id){ //DB 입력 성공

            
                $return->status = "200";
                $return->msg = "success";
            
            }
   
        //}
        

        return response()->json($return, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);

        //return view('user.profile', ['user' => User::findOrFail($id)]);
    }

    public function regist_jobhistory(Request $request){
        
        $return = new \stdClass;
        
        $login_user = Auth::user();
        $user_id = $login_user->getId();
                    
        $result = JobHistory::insertGetId([
            'user_id'=> $user_id ,
            'position' => $request->position,   
            'company_name' => $request->company_name,                 
            'department' => $request->department,
            'local' => $request->local,
            'pay' => $request->pay,
            'job_part' => $request->job_part,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'period_year' => $request->period_year,
            'period_mon' => $request->period_mon, 
            'created_at' => Carbon::now()
        ]);

        if($result){ //DB 입력 성공        
            $return->status = "200";
            $return->msg = "success";
        }   


        return response()->json($return, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);
    }

    public function login(Request $request){
        $user = User::where('email' , $request->email)->where('leave','N')->first();

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


    public function certify_email(Request $request){// 이메일 인증번호 발송
        $return = new \stdClass;
        $email = trim($request->email);

        $email_cnt = User::where('email',$email)->count();

        if($email_cnt){
            $return->usable = "500";
            $return->msg = "이미 사용중인 이메일입니다.";
            $return->email = $email;
        }else{

            $code = mt_rand(100000,999999);
            $result_insert = EmailCode::insertGetId([
                'email' => $email, 
                'code' => $code, 
                'created_at' => Carbon::now(),
            ]);

            if($result_insert){
                $title = "[파이널매칭] 메일 인증 번호"; 
                $subject = "=?EUC-KR?B?".base64_encode(iconv("UTF-8","EUC-KR",$title))."?=";
                
                $content = "파이널매칭 메일 인증 번호 보내드립니다.\n\n 인증번호는 : ".$code." 입니다.";
                
                $mail = new PHPMailer(true);         
                
                try {
                    //Server settings
                    $mail->isSMTP();                                            // Send using SMTP
                    $mail->Host       = env('MAIL_HOST');                    // Set the SMTP server to send through
                    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
                    $mail->Username   = env('MAIL_USERNAME');                     // SMTP username
                    $mail->Password   = env('MAIL_PASSWORD');                               // SMTP password
                    $mail->CharSet = 'utf-8'; 
                    $mail->Encoding = "base64";
                    $mail->SMTPSecure = 'ssl';          
                    $mail->Port       = env('MAIL_PORT');                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
                    
            
                    //Recipients
                    $mail->setFrom(env('MAIL_FROM_ADDRESS'), '파이널매칭팀');

                    $mail->addAddress($email);     // Add a recipient
                    
                    // Content
                    $mail->isHTML(true);                                  // Set email format to HTML
                    $mail->Subject = $subject;
                    $mail->Body    = $content;
            
                    $result = $mail->send();
                    //echo 'Message has been sent';
                    //$result =  true;
                } catch (Exception $e) {
                    //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                    $result = false;
                }
                
                if($result){
                    $return->status = "200";
                    $return->msg = "메일이 발송되었습니다.";
                }else{
                    $return->status = "500";
                    $return->msg = "인증메일 발송 실패";
                } 
            }else{
                $return->status = "500";
                $return->msg = "코드발급 실패, 관리자에게 문의하세요.";
            }
                       
        }

        return response()->json($return, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);
    }

    public function check_email_code(Request $request){ // 이메일 인증번호 검증
        
        $return = new \stdClass;

        $cnt = EmailCode::where('code',$request->code)->where('email',$request->email)->count();
    
        if($cnt){
            $return->status = "200";
            $return->msg = "유효한 인증입니다.";
            EmailCode::where('code',$request->code)->where('email',$request->email)->delete();
        }else{
            $return->status = "500";
            $return->msg = "잘못된 인증번호 입니다.";
        }    

        return response()->json($return, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);
    }

    public function login_check(Request $request){
        
        $return = new \stdClass;
        $login_user = Auth::user();
        $user_id = $login_user->getId();

        if(Auth::check()){
            $return->status = "200";
            $return->login_status = "Y";
        }    

        return response()->json($return, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);
        
    }

    public function user_info(){
        $return = new \stdClass;
        $login_user = Auth::user();
        $user_id = $login_user->getId();

    
        $user_info = User::leftJoin('apply_infos', function($join) {
                $join->on('users.id', '=', 'apply_infos.user_id');
                })
                ->select(
                    'users.id as id',
                    'users.email as email',
                    'users.name as name',
                    'users.phone as phone',
                    'users.user_type as user_type',
                    'apply_infos.profile_img as profile_img',
                    'apply_infos.addr1 as addr1',
                    'apply_infos.addr2 as addr2',
                    'apply_infos.birthday as birthday',
                    'apply_infos.gender as gender',
                    'apply_infos.career_type as career_type',
                    'apply_infos.last_position as last_position',
                    'apply_infos.interest as interest',
                    'apply_infos.condition as condition',
                    'apply_infos.min_pay as min_pay'
                )
                ->where('users.id', $user_id)
                ->first();

        

        if($user_info){
            $return->data = $user_info;

            if($user_info->user_type == 1){
                $com_info = CompanyInfo::where('user_id',$user_info->id)->first();
                $return->company_id = $com_info->id;
            }

            $return->status = "200";
            $return->login_status = "Y";
        }else{
            $return->status = "500";
            $return->login_status = "N";
        }    

        return response()->json($return, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);
    }
    

    public function find_user_id(Request $request){
        $user = User::where('phone' , $request->phone)->first();
        
        if (isset($user->id)) {
            echo("사용자 아이디는 ".$user->user_id." 입니다.");       
        }else{
            echo("등록되지 않은 연락처 입니다.");       
        }
    }

    public function profile_list(Request $request){
        $start_no = $request->start_no;
        $row = $request->row;
        
        $rows = User::join('apply_infos', 'apply_infos.user_id', '=', 'users.id')
        ->select('users.id as user_id','name','profile_img','career_type')->where('user_type','0')->orderBy('users.id', 'desc')->limit($row)->get();

        $list = new \stdClass;

        $list->status = "200";
        $list->msg = "success";
        $list->cnt = count($rows);
        $list->data = $rows;
        
        return response()->json($list, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);
        
    }

    public function company_list(Request $request){
        $start_no = $request->start_no;
        $row = $request->row;
        
        $rows = CompanyInfo::select('company_infos.id as company_id','logo_img','company_name','job_type') 
                            ->where('id' ,">=", $start_no)
                            ->orderBy('id', 'desc')
                            ->limit($row)
                            ->get();

        $list = new \stdClass;

        $list->status = "200";
        $list->msg = "success";
        $list->cnt = count($rows);
        $list->data = $rows;
        
        return response()->json($list, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);
        
    }

    public function popular_list(Request $request){
        $rows = Popular::join('company_infos', 'populars.company_id', '=', 'company_infos.id')
                        ->select('company_infos.id as company_id','logo_img','company_name','job_type') 
                        ->orderBy('order_no','asc')
                        ->get();

        $return = new \stdClass;

        $return->status = "200";
        $return->cnt = count($rows);
        $return->data = $rows ;
        
        return response()->json($return, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);
    }

    public function new_list(Request $request){
        
        $rows = User::join('apply_infos', 'apply_infos.user_id', '=', 'users.id')
        ->select('users.id as user_id','name','profile_img','career_type')->where('user_type','0')->orderBy('users.id', 'desc')->limit(10)->get();

        $list = new \stdClass;

        $list->status = "200";
        $list->msg = "success";
        $list->cnt = count($rows);
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

    public function company_detail(Request $request){

        $list = new \stdClass;

        $user_id = "s";
        
        if($request->bearerToken() != ""){
            $tokens = explode('|',$request->bearerToken());
            $token_info = DB::table('personal_access_tokens')->where('id',$tokens[0])->first();
            $user_id = $token_info->tokenable_id;
        }

        
        $rows = CompanyInfo::where('company_infos.id',$request->company_id)
                        ->select(
                            'company_infos.id as company_id',
                            'logo_img',
                            'company_name',
                            'addr1',
                            'addr2',
                            'biz_item',
                            'biz_type',
                            'reg_no',
                            'job_type',
                            'introduction',
                            'history',
                            'members',
                            'type',
                            'com_size',
                            'pay',
                            'condition',
                            'investment',
                            'sales',
                            'profit',
                            DB::raw('(select count(*) from wishes where company_infos.id = wishes.company_id and wishes.user_id="'.$user_id.'" ) as wished '),
                            DB::raw('(select phone from users where company_infos.user_id = users.id) as phone '),
                        )->first();
        
        if($rows){
            $com_images =CompanyImage::where('company_id',$rows->company_id)
                        ->select(
                            '*'
                        )
                        ->get();
            $fin_images =FinancialImage::where('company_id',$rows->company_id)
                            ->select(
                                '*'
                            )
                            ->get();

            $rows->com_images = $com_images;                    
            $rows->financial_images = $fin_images;
            
            $list->status = "200";
            $list->msg = "success";
            $list->data = $rows;
        }else{
            $list->status = "500";
            $list->msg = "없는 정보 입니다.";
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

    public function update_user_info(Request $request){
        //dd($request);
        $return = new \stdClass;

        $return->status = "200";
        $return->msg = "변경 완료";

        $login_user = Auth::user();
        $user_id = $login_user->getId();

        $apply_id = ApplyInfo::where('user_id', $user_id)->first();
        
        $result = ApplyInfo::where('id', $apply_id->id)->update([
            'addr1' => $request->addr1,                 
            'addr2' => $request->addr2,
            'birthday' => $request->birthday,
            'gender' => $request->gender,
            'career_type' => $request->career_type,
            'last_position' => $request->last_position,
            'interest' => $request->interest,
            'condition' => $request->condition,
            'min_pay' => $request->min_pay, 
            'profile_img' => $request->profile_img
        ]);

        if(!$result){
            $return->status = "500";
            $return->msg = "변경 실패";
        }

        return response()->json($return, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);

    }

    public function update_company_info(Request $request){
        //dd($request);
        $return = new \stdClass;

        $return->status = "200";
        $return->msg = "변경 완료";

        $login_user = Auth::user();
        $user_id = $login_user->getId();

        $company_id = CompanyInfo::where('user_id', $user_id)->first();
        
        $result = CompanyInfo::where('id', $company_id->id)->update([
            'company_name' => $request->company_name,                 
            'biz_item' => $request->biz_item,
            'biz_type' => $request->biz_type,
            'reg_no' => $request->reg_no,
            'job_type' => $request->job_type,
            'history' => $request->history,
            'addr1' => $request->addr1,
            'addr2' => $request->addr2,
            'introduction' => $request->introduction, 
            'members' => $request->members, 
            'type' => $request->type, 
            'com_size' => $request->com_size, 
            'pay' => $request->pay, 
            'condition' => $request->condition, 
            'investment' => $request->investment, 
            'sales' => $request->sales, 
            'profit' => $request->profit, 
            'logo_img' => $request->logo_img
        ]);

        if(!$result){
            $return->status = "500";
            $return->msg = "변경 실패";
        }

        return response()->json($return, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);

    }

    public function leave(Request $request){
        //dd($request);
        $return = new \stdClass;
        $login_user = Auth::user();

        $return->status = "200";
        $return->msg = "탈퇴처리 완료";

        $user_id = $login_user->id;

        $result = User::where('id', $user_id)->update(['leave' => 'Y']);

        if(!$result){
            $return->status = "500";
            $return->msg = "탈퇴처리 실패";
        }

        return response()->json($return, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);

    }

    public function not_login(){
        $return = new \stdClass;
    
        $return->status = "500";
        $return->msg = "Not Login";

        return response()->json($return, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);
    }

    public function company_search(Request $request){
        $keyword = $request->keyword;
        $type = $request->type;
        $com_size = $request->com_size;
        $pay = $request->pay;
        $condition = $request->condition;
        $addr1 = $request->addr1;
        $biz_item = $request->biz_item;

        SearchKeyword::insert([
            'keyword'=> $keyword ,
            'created_at' => Carbon::now()
        ]);

        $rows = CompanyInfo::select('company_infos.id as company_id','logo_img','company_name','job_type') 
                            ->where('company_name' ,"like", "%".$keyword."%")
                            ->when($type, function ($query, $type) {
                                return $query->where('type' , $type);
                            })
                            ->when($com_size, function ($query, $com_size) {
                                return $query->where('com_size' , $com_size);
                            })
                            ->when($pay, function ($query, $pay) {
                                return $query->where('pay' , $pay);
                            })
                            ->when($condition, function ($query, $condition) {
                                return $query->where('condition' , $condition);
                            })
                            ->when($addr1, function ($query, $addr1) {
                                return $query->where('addr1' , $addr1);
                            })
                            ->when($biz_item, function ($query, $biz_item) {
                                return $query->where('biz_item' , $biz_item);
                            })
                            ->orderBy('id', 'desc')
                            ->get();

        $list = new \stdClass;

        $list->status = "200";
        $list->msg = "success";
        $list->cnt = count($rows);
        $list->data = $rows;
        
        return response()->json($list, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);
        
    }

    public function profile_search(Request $request){
        $keyword = $request->keyword;
        
        SearchKeyword::insert([
            'keyword'=> $keyword ,
            'created_at' => Carbon::now()
        ]);

        $rows = User::join('apply_infos', 'apply_infos.user_id', '=', 'users.id')
                            ->select('users.id as user_id','name','profile_img','career_type')
                            ->where('user_type','0')
                            ->where('name' ,"like", "%".$keyword."%")
                            ->get();

        $list = new \stdClass;

        $list->status = "200";
        $list->msg = "success";
        $list->cnt = count($rows);
        $list->data = $rows;
        
        return response()->json($list, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);
        
    }

    public function search_option_company(Request $request){
        
        $key = $request->key;

        $rows = CompanyInfo::select($key) 
                            ->distinct($key)
                            ->get();

        $list = new \stdClass;

        $list->status = "200";
        $list->msg = "success";
        $list->cnt = count($rows);
        $list->data = $rows;
        
        return response()->json($list, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);
        
    }


    public function search_keyword_list(Request $request){
        $keyword = $request->keyword;

        $rows = SearchKeyword::select('keyword')
                            ->where('keyword' ,"like", "%".$keyword."%")
                            ->get();
                            $key = $request->key;

        $list = new \stdClass;

        $list->status = "200";
        $list->msg = "success";
        $list->cnt = count($rows);
        $list->data = $rows;
        
        return response()->json($list, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);
        
    }


    

    


}

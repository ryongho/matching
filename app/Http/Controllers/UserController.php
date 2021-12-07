<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ApplyInfo;
use App\Models\Hotel;
use App\Models\EmailCode;
use App\Models\CompanyImage;
use App\Models\FinancialImage;
use App\Models\CompanyInfo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

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
        

        echo(json_encode($return));

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
                'user_type' => 0,
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
                            'user_id'=> $result ,
                            'file_name'=> $cimage ,
                            'order_no'=> $no ,
                            'created_at' => Carbon::now()
                        ]);
    
                        $no++;
                    }
                    
                    $no2 = 1; 
    
                    $fimages = explode(",",$request->financial_img);
                    foreach( $fimages as $fimage){
                    
                        $result_img = FinancialImage::insertGetId([
                            'user_id'=> $result ,
                            'file_name'=> $fimage ,
                            'order_no'=> $no2 ,
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
        

        echo(json_encode($return));

        //return view('user.profile', ['user' => User::findOrFail($id)]);
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
            
            //dd($token->plainTextToken);    
        }else{
            $return->status = "500";
            $return->msg = "아이디 또는 패스워드가 일치하지 않습니다.";
            $return->email = $request->email;
        }

        echo(json_encode($return));
    }

    public function logout(Request $request){
        $user_info = Auth::user();
        $user = User::where('id', $user_info->id)->first();
        $user->tokens()->delete();

        $return = new \stdClass;
        $return->status = "200";
        $return->msg = "success";

        echo(json_encode($return));
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
                    $mail->Host       = 'smtp.naver.com';                    // Set the SMTP server to send through
                    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
                    $mail->Username   = 'yongho0720';                     // SMTP username
                    $mail->Password   = 'dydgh123';                               // SMTP password
                    $mail->CharSet = 'utf-8'; 
                    $mail->Encoding = "base64";
                    $mail->SMTPSecure = 'ssl';          
                    $mail->Port       = 465;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
            
                    //Recipients
                    $mail->setFrom('yongho0720@naver.com', '파이널매칭팀');
            
                    
                    $mail->addAddress($email);     // Add a recipient
                    
            
                    // Content
                    $mail->isHTML(true);                                  // Set email format to HTML
                    $mail->Subject = $subject;
                    $mail->Body    = $content;
            
                    $mail->send();
                    //echo 'Message has been sent';
                    $result =  true;
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

        echo(json_encode($return));
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

        echo(json_encode($return));
    }

    public function login_check(Request $request){
        
        $return = new \stdClass;
        $login_user = Auth::user();
        $user_id = $login_user->getId();

        if(Auth::check()){
            $return->status = "200";
            $return->login_status = "Y";
        }    

        echo(json_encode($return));
        
    }
    

    public function find_user_id(Request $request){
        $user = User::where('phone' , $request->phone)->first();
        
        if (isset($user->id)) {
            echo("사용자 아이디는 ".$user->user_id." 입니다.");       
        }else{
            echo("등록되지 않은 연락처 입니다.");       
        }
    }

    public function list(Request $request){
        $start_no = $request->start_no;
        $row = $request->row;
        
        $rows = User::where('id' ,">=", $start_no)->where('user_type','0')->orderBy('id', 'desc')->orderBy('id')->limit($row)->get();

        $list = new \stdClass;

        $list->status = "200";
        $list->msg = "success";
        $list->cnt = count($rows);
        $list->data = $rows;
        
        echo(json_encode($list));
        
    }
    

    public function info(){
        //dd($request);
        $return = new \stdClass;
        $login_user = Auth::user();

        $return->status = "200";
        $return->data = $login_user;

        if($login_user->user_type == 1){
            $hotel_info = Hotel::where('partner_id',$login_user->id)->first();
            if($hotel_info){
                $return->hotel_id = $hotel_info->id;
            }
            
        }

        echo(json_encode($return));

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

        echo(json_encode($return));

    }

    public function update_info(Request $request){
        //dd($request);
        $return = new \stdClass;

        $return->status = "200";
        $return->msg = "변경 완료";
        
        $result = User::where('id', $request->user_id)->update([
            'name'=> $request->name ,
            'nickname'=> $request->nickname ,
            'email' => $request->email, 
            'phone' => $request->phone, 
            'user_type' => $request->user_type,
            'push' => $request->push,
            'push_event' => $request->push_event,
        ]);

        if(!$result){
            $return->status = "500";
            $return->msg = "변경 실패";
        }

        echo(json_encode($return));

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

        echo(json_encode($return));

    }

    public function not_login(){
        $return = new \stdClass;
    
        $return->status = "500";
        $return->msg = "Not Login";

        echo(json_encode($return));
    }

    


}

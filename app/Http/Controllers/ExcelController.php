<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ApplyInfo;
use App\Models\Profile;
use App\Models\JobHistory;
use App\Models\CompanyInfo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\DB;

//use Maatwebsite\Excel\Concerns\FromQuery;
//use Maatwebsite\Excel\Concerns\Exportable;
//use Maatwebsite\Excel\Facades\Excel;
use PHPExcel; 
use PHPExcel_IOFactory;
//use App\Exports\UserList;

class ExcelController extends Controller
{
    
    public function user_list(Request $request){
        $type = $request->type;     
        $start_date = $request->start_date;     
        $end_date = $request->end_date;
        $keyword = $request->keyword;
        
        $rows = User::select(
            'users.id as user_id',
            'users.email as email',
            'users.name as name',
            'users.user_type as user_type',
            'users.phone as phone',
            'users.created_at as created_at',
            'users.last_login as last_login',
            'company_name as company_name',
            'company_infos.job_type as company_type',
            'company_infos.logo_img as logo_img',
            'apply_infos.profile_img as profile_img',
        )->leftJoin('apply_infos', function($join) {
            $join->on('users.id', '=', 'apply_infos.user_id');
        })
        ->leftJoin('company_infos', function($join) {
            $join->on('users.id', '=', 'company_infos.user_id');
        })
        ->whereIn('user_type',['0','1'])
        ->when($type, function ($query, $type) {
            if($type == "전체"){
                return;
            }else if($type == "일반회원"){
                return $query->where('users.user_type', 0 );
            }else{
                return $query->where('company_infos.job_type', $type );
            }
            
        })
        ->when($keyword, function ($query, $keyword) {
            return $query->where('company_name', 'like', "%".$keyword."%")->orWhere('users.name', 'like', "%".$keyword."%");
        })
        ->whereBetween('users.created_at',[$start_date.' 00:00:00',$end_date.' 23:59:59'])
        ->orderBy('users.id', 'desc')
        ->get();

        $list = array();
        $i = 0;

        foreach($rows as $row){
            
            $list[$i]['user_id'] = $row->user_id;
            $list[$i]['user_type'] = $row->user_type;
            $list[$i]['phone'] = $row->phone;
            $list[$i]['email'] = $row->email;
            $list[$i]['created_at'] = $row->created_at;
            $list[$i]['last_login'] = $row->last_login;

            if($row->user_type == 0){ // 일반회원
                $list[$i]['name'] = $row->name;
                $list[$i]['type'] = "일반회원";
                $list[$i]['thumb_img'] = $row->profile_img;
            }else{
                $list[$i]['name'] = $row->company_name;
                $list[$i]['type'] = $row->company_type;
                $list[$i]['thumb_img'] = $row->logo_img;
            }
           
            $i++;
        }
        dd($list);
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);
        date_default_timezone_set('Europe/London');

        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Browser');

        /** Include PHPExcel */
        //require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';


        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                                    ->setLastModifiedBy("Maarten Balliauw")
                                    ->setTitle("Office 2007 XLSX Test Document")
                                    ->setSubject("Office 2007 XLSX Test Document")
                                    ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                                    ->setKeywords("office 2007 openxml php")
                                    ->setCategory("Test result file");


        // Add some data
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', '이메일')
                    ->setCellValue('B1', '이름')
                    ->setCellValue('C1', '구분')
                    ->setCellValue('D1', '휴대폰번호')
                    ->setCellValue('E1', '생성일')
                    ->setCellValue('F1', '최종로그인');
        $i = 2;
        foreach ($list as $row){

            $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.$i, $row['email'])
                        ->setCellValue('B'.$i, $row['name'])
                        ->setCellValue('C'.$i, $row['type'])
                        ->setCellValue('D'.$i, $row['phone'])
                        ->setCellValue('E'.$i, $row['created_at'])
                        ->setCellValue('F'.$i, $row['last_login']);
            $i++;
        }
                                
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('user_list');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="user_list.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;

        
    }
    

    

    

    


}

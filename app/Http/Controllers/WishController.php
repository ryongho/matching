<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wish;
use App\Models\CompanyInfo;
use App\Models\Profile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WishController extends Controller
{
    

    public function toggle_company(Request $request)
    {
        //dd($request);
        $return = new \stdClass;

        $login_user = Auth::user();
        $user_id = $login_user->getId();

        $cnt = Wish::where('company_id',$request->company_id)->where('user_id',$user_id)->count();

        if($cnt){
            
            Wish::where('company_id',$request->company_id)->where('user_id',$user_id)->delete();
            $return->status = "200";
            $return->added = 'N';

        }else{
            Wish::insert([
                'user_id'=> $user_id ,
                'company_id'=> $request->company_id ,
                'created_at'=> Carbon::now(),
            ]);

            $return->status = "200";
            $return->added = 'Y';

            
        }


        echo(json_encode($return));
        
    }

    public function toggle_profile(Request $request)
    {
        //dd($request);
        $return = new \stdClass;

        $login_user = Auth::user();
        $user_id = $login_user->getId();

        $cnt = Wish::where('profile_id',$request->profile_id)->where('user_id',$user_id)->count();

        if($cnt){
            
            Wish::where('profile_id',$request->goods_id)->where('user_id',$user_id)->delete();
            $return->status = "200";
            $return->added = 'N';

        }else{
            Wish::insert([
                'user_id'=> $user_id ,
                'profile_id'=> $request->goods_id ,
                'created_at'=> Carbon::now(),
            ]);

            $return->status = "200";
            $return->added = 'Y';

            
        }


        echo(json_encode($return));
        
    }

    public function list_company(){

        $return = new \stdClass;

        $login_user = Auth::user();
        $user_id = $login_user->getId();
        
        $wishs = Wish::select('company_id')->where('user_id',$user_id)->orderby('created_at','desc')->get();

        $rows = array();
        $i =0;

        //dd($wishs);

        foreach($wishs as $wish){
            
            $row = CompanyInfo::select('company_infos.id as company_id','logo_img','company_name','com_size','addr1','addr2')
                    ->where('id', $wish->company_id)  
                    ->first();
    
            if($row){
                $rows[$i] = $row;
                $i++;
            }
            
        }
        
        $return->status = "200";
        $return->cnt = count($rows);
        $return->data = $rows;

        echo(json_encode($return));
        
    }

    public function list_profile(){

        $return = new \stdClass;

        $login_user = Auth::user();
        $user_id = $login_user->getId();
        
        $wishs = Wish::select('profile_id')->where('user_id',$user_id)->orderby('created_at','desc')->get();

        $rows = array();
        $i =0;

        //dd($wishs);

        foreach($wishs as $wish){
            
            $row = Profile::select('id as profile_id','profile_img','name','interest','addr')
                    ->where('id', $wish->profile_id)  
                    ->first();
    
            if($row){
                $rows[$i] = $row;
                $i++;
            }
            
        }
        
        $return->status = "200";
        $return->cnt = count($rows);
        $return->data = $rows;

        echo(json_encode($return));
        
    }

    



}

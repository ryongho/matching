<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wish;
use App\Models\Goods;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\Comparaison;
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
            
            Wish::where('company_id',$request->goods_id)->where('user_id',$user_id)->delete();
            $return->status = "200";
            $return->added = 'N';

        }else{
            Wish::insert([
                'user_id'=> $user_id ,
                'company_id'=> $request->goods_id ,
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

    public function list_comapny(){

        $return = new \stdClass;

        $login_user = Auth::user();
        $user_id = $login_user->getId();

         
        $order_by = "company_infos.company_name";
        $order_key = "asc";

        if($request->order_by == "name"){
            $order_by = "company_infos.company_name";
            $order_key = "asc";    
        }else if($request->order_by == "regist"){
            $order_by = "wish.id";
            $order_key = "desc";
        }
    
        
        $rows = Wish::join('company_infos', 'company_infos.id', '=', 'wish.company_id')
                    ->select('company_infos.id as company_id','logo_img','company_name','com_size','addr1','addr2') 
                    ->orderby('wish.created_at','desc')
                    ->get();


        $return->status = "200";
        $return->cnt = count($rows);
        $return->data = $rows;

        echo(json_encode($return));
        
    }



}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;


class PaymentController extends Controller
{
    public function regist(Request $request)
    {
        //dd($request);

        $login_user = Auth::user();

        $user_id = $login_user->id;

        $return = new \stdClass;

        
        $result = Payment::insert([
            'apply_id'=> $request->apply_id ,
            'imp_uid'=> $request->imp_uid ,
            'merchant_uid'=> $request->merchant_uid ,
            'order_name'=> $request->order_name ,
            'user_id'=> $user_id ,
            'price'=> $request->price ,
            'pay_type'=> $request->pay_type ,
            'pg'=> $request->pg ,
            'pg_orderno'=> $request->pg_orderno ,
            'detail'=> $request->detail ,
            'payed_at'=> $request->payed_at ,
            'status'=> $request->status ,
            'buyer_name'=> $request->buyer_name ,
            'buyer_phone'=> $request->buyer_phone ,
            'buyer_email'=> $request->buyer_email ,
            'buyer_addr'=> $request->buyer_addr ,
            'created_at' => Carbon::now(),
        ]);

        if($result){
            $return->status = "200";
            $return->msg = "success";
        }else{
            $return->status = "500";
            $return->msg = "fail";
        }

        return response()->json($return, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);       

    }

    public function list(Request $request){
        $start_no = $request->start_no;
        $row = $request->row;
        
        $rows = User::where('id' ,">=", $start_no)->where('user_type','1')->orderBy('id', 'desc')->limit($row)->get();

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

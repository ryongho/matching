<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AreaController extends Controller
{

    public function area_list1(Request $request){

        $rows = DB::table('area_codes')
                ->select('region_1depth_name as name')
                ->distinct()
                ->get();

        $return = new \stdClass;

        $return->status = "200";
        $return->cnt = count($rows);
        $return->data = $rows ;

        return response()->json($return, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);

    }

    public function area_list2(Request $request){
        $area1 = $request->area1_name;

        $rows = DB::table('area_codes')
                ->select('region_2depth_name as name')
                ->distinct()
                ->whereNotNull('region_2depth_name')
                ->where('region_1depth_name',$area1)
                ->where('region_2depth_name', 'Not like', '% %')
                ->get();

        $return = new \stdClass;

        $return->status = "200";
        $return->cnt = count($rows);
        $return->data = $rows ;

        return response()->json($return, 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);

    }

    



}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TwoDController extends Controller
{
    public function history()
    {
        $data = TwoDWonNumber::orderBy('id','desc')->get();
        if(count($data) > 0){
            return response()->json([
                'status'    => true,
                'data'      => $data   
            ]);
        }else{
            return response()->json([
                'status'    => false,
                'message'   => 'There is no data'
            ]);
        }
    }
}

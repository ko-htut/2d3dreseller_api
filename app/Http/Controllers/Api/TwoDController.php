<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Goutte;

use App\Models\TwoDWonNumber;

class TwoDController extends Controller
{

    public function check2DLive()
    {
        $dw = date("w");
        $status = true;
        if($dw == 0 || $dw == 6){
            return response()->json([
                'status'    => false,
                'message'   => 'Sat/Sun days close'
            ]);
        }

        $currentHour = Carbon::now()->format('H');
        $currentMin = Carbon::now()->format('i');

        if($currentHour > 17 || $currentHour < 9){
            return response()->json([
                'status'    => false,
                'message'   => 'Server sleep time'
            ]);
        }

        $crawler = Goutte::request('GET', 'https://classic.set.or.th/set/holiday.do?language=en&country=US');
        $result = $crawler->filter('#maincontent .row .table-responsive')->each(function ($node) {
            $item['status'] = true;
            $item['title'] = $node->filter('thead th')->text();
            $day_array = [];
            $days = $node->filter('tbody tr')->each(function ($dayNode){
                $row['year'] = now()->format('Y');
                $row['month']  = $dayNode->filter('td')->eq(0)->text();
                $row['day'] = $dayNode->filter('td')->eq(1)->text();
                $row['date'] = $dayNode->filter('td')->eq(2)->text();
                $row['note'] = $dayNode->filter('td')->eq(3)->text();
                return $row;
            });

            $item['data'] = $days;
            return $item;
        });
        $holiday = $result[0];
        $holiday = json_decode($holiday, true);
        if(isset($holiday)){
            foreach($holiday->data as $row){
                $item['date'] = explode(" ", $row->date);
                if($item['date'][0] == Carbon::now()->format('d') && $item['date'][1] == Carbon::now()->format('F')){
                    return response()->json([
                        'status'    => false,
                        'message'   => $row->note
                    ]);
                }
            }
        }
        

        return response()->json([
            'status'    => true,
            'message'   => '2D Live is now'
        ]);
    }

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

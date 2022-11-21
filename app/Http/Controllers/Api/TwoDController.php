<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Goutte;
use Log;

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
        if(isset($result[0])){
            $holiday = $result[0];
            Log::info(json_encode($holiday));
            //$holiday = json_decode($holiday);
            if(isset($holiday)){
                foreach($holiday['data'] as $row){
                    $item['date'] = explode(" ", $row['date']);
                    if($item['date'][0] == Carbon::now()->format('d') && $item['date'][1] == Carbon::now()->format('F')){
                        return response()->json([
                            'status'    => false,
                            'message'   => $row['note']
                        ]);
                    }
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

        $dateCollection = [];
        $savedArray = [];

        foreach($data as $row){
            if(array_key_exists($row->date, $dateCollection)){
                $tmp = $dateCollection[$row->date];
                array_push($tmp, $row);
                $dateCollection[$row->date] = $tmp;
            }else{
                $tmp = [];
                array_push($tmp, $row);
                $dateCollection[$row->date] = $tmp;
            }
        }

        foreach($dateCollection as $row){
            $item['date'] = $row[0]->date;
            $item['item'] = collect($row)->sortBy('time_type')->flatten(1); //TwoDResultResource::collection($row)
            array_push($savedArray, $item);
        }

        //return response()->json( new TwoDResultCollection($data), 200);

        if(count($savedArray) > 0){
            return response()->json([
                'status'    => true,
                'message'   => 'Success',
                'data'      => $savedArray
            ]);
        }else{
            return response()->json([
                'status'    => false,
                'message'   => 'There is no events'
            ]);
        }

    }

    public function saveTwoDWonNumber()
    {
        $currentHour = Carbon::now()->format('H');
        $currentMin = Carbon::now()->format('i');
        $time_type = null;
        if($currentHour > "6" && $currentHour < "12" ||
            $currentHour == "6" && $currentMin >= "00" ||
            $currentHour == "12" && $currentMin < "02"
        ){
            $time_type = "AM";
        }else{
            $time_type = "PM";
        }

        try{
            $crawler = Goutte::request('GET', 'https://classic.set.or.th/mkt/sectorialindices.do?language=en&country=US');
            $item['date'] = Str::replace('* Market data provided for educational purpose or personal use only, not intended for trading purpose. * Last Update ','',$crawler->filter('#maincontent .row .table-info caption')->text());
            $item['set'] = $crawler->filter('#maincontent .row .table-info tbody tr td')->eq(1)->text();
            $item['val'] = $crawler->filter('#maincontent .row .table-info tbody tr td')->eq(7)->text();
            $item['result'] = Str::substr($item['set'], -1) . Str::substr(Str::before($item['val'], '.'), -1);

            $thai = [
                'set'           => $item['set'],
                'val'           => $item['val'],
                'number'        => $item['result'],
                'time_type'     => $time_type,
                'date'          => now()->toDateString(),
                'created_at'    => now(),
                'updated_at'    => now()
            ];

            $check = TwoDWonNumber::where('time_type', $time_type)
                                    ->where('date', now()->toDateString())
                                    ->first();
            if(!$check){
                TwoDWonNumber::insert($thai);
            }
            echo "Finish";
        }catch(\Exception $e){
            Log::error($e->getMessage());
        }
    }
}

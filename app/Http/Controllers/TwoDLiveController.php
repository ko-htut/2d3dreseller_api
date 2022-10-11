<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Goutte;
use Log;

use App\Models\TwoDWonNumber;
use App\Models\TwoDChangeNumber;

class TwoDLiveController extends Controller
{
    public function update(){

        try{
            $crawler = Goutte::request('GET', 'https://classic.set.or.th/mkt/sectorialindices.do?language=en&country=US');
            $item['date'] = Str::replace('* Market data provided for educational purpose or personal use only, not intended for trading purpose. * Last Update ','',$crawler->filter('#maincontent .row .table-info caption')->text());
            $item['set'] = $crawler->filter('#maincontent .row .table-info tbody tr td')->eq(1)->text();
            $item['val'] = $crawler->filter('#maincontent .row .table-info tbody tr td')->eq(7)->text();
            $item['result'] = Str::substr($item['set'], -1) . Str::substr(Str::before($item['val'], '.'), -1);
    
            $wonNumber = TwoDWonNumber::whereDate('date', now()->toDateString())
                                        ->select('number', 'set', 'val', 'time_type', 'date')
                                        ->get();
                        
            $currentHour = Carbon::now()->format('H');
            $currentMin = Carbon::now()->format('i');
            $time_type = null;
            $toDay = now()->toDateString();
    
            if($currentHour > "6" && $currentHour < "12" ||
                $currentHour == "6" && $currentMin >= "00" ||
                $currentHour == "12" && $currentMin < "02"
            ){
                $time_type = "AM";
            }else{
                $time_type = "PM";
            }
    
            $lastNumber = TwoDChangeNumber::orderBy('id','desc')
                                ->where('date', $toDay)
                                ->where('time_type', $time_type)
                                ->first();
            if($lastNumber){
                if($lastNumber->number != $item['result']){
                    $data = new TwoDChangeNumber;
                    $data->number = $item['result'];
                    $data->time_type = $time_type;
                    $data->date = $toDay;
                    $data->save();
                }
            }else{
                $data = new TwoDChangeNumber;
                $data->time_type = $time_type;
                $data->number = $item['result'];
                $data->date = $toDay;
                $data->save();
            }
                        
            $changeNumber = TwoDChangeNumber::whereDate('date', $toDay)
                                    ->where('time_type', $time_type)
                                    ->select('number')
                                    ->get();
    
            $dw = date("w");
            $status = true;
            if($dw == 0 || $dw == 6){
                $status = false;
            }else{
                $status = true;
            }
            
            $result = [
                'status' => $status,
                'dw' => now()->format('l'),
                'date' => $item['date'],
                'set' => $item['set'],
                'val' => $item['val'],
                'result' => $item['result'],
                'updated_at' => now(),
                'won_number' => $wonNumber,
                'change_number' => $changeNumber
            ];
            return $result;
        }catch(\Exception $e){
            Log::info("2D Live Error ==> ".$e);
        }
        
    }
}

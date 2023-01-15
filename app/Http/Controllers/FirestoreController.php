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

class FirestoreController extends Controller
{
    //
    // set data to firestore 
   

    public function twoDLive(){

   
            $crawler = Goutte::request('GET', 'https://classic.set.or.th/mkt/sectorialindices.do?language=en&country=US');
            $item['date'] = Str::replace('* Market data provided for educational purpose or personal use only, not intended for trading purpose. * Last Update ','',$crawler->filter('#maincontent .row .table-info caption')->text());
            $item['set'] = $crawler->filter('#maincontent .row .table-info tbody tr td')->eq(1)->text();
            $item['val'] = $crawler->filter('#maincontent .row .table-info tbody tr td')->eq(7)->text();
            $item['result'] = Str::substr($item['set'], -1) . Str::substr(Str::before($item['val'], '.'), -1);
    
            $currentHour = Carbon::now()->format('H');
            $currentMin = Carbon::now()->format('i');
            $time_type = null;
            // to myanmar time zome
            
            // tom
            $toDay = now()->toDateTime();
    
            if($currentHour > "6" && $currentHour < "12" ||
                $currentHour == "6" && $currentMin >= "00" ||
                $currentHour == "12" && $currentMin < "02"
            ){
                $time_type = "AM";
            }else{
                $time_type = "PM";
            }
              $live = [
                            'set' => $item['set'],
                            'val' => $item['val'],
                            'result' => $item['result'],
                            'updated_at' => $item['date'],
                            'time_type' => $time_type,
                            'now' => $toDay
                        ];
        $data['live'] = $live;   
        $db = app('firebase.firestore')->database();
        $docRef = $db->collection('live')->document('2d');
        $docRef->set($data);
        return $data;
            
   
    }
}
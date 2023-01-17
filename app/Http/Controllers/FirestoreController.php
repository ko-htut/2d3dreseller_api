<?php

namespace App\Http\Controllers;

use App\Models\Holidays;
use App\Models\TwoDResult;
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

   
        $currentTime = Carbon::now()->format('Hi');
        $currentTime = (int)$currentTime;
        
        $currentHour = Carbon::now()->format('H');
        $currentMin = Carbon::now()->format('i');
        $time_type = null;
        $status = true;
        $dw = date("w");
        
        $holiday = Holidays::where('year', now()->format('Y'))
                        ->where('month', now()->format('F'))
                        ->where('date', now()->format('d'))
                        ->first();

        if($dw == 0 || $dw == 6){

            $status = false;
            $stopResult = TwoDResult::where('time_type', 'PM')
                                    ->where('country', 'Thai')
                                    ->where('status', 1)
                                    ->orderBy('id','desc')
                                    ->first();
                if(!$stopResult){
                    $live = [
                        'set' => '--',
                        'val' => '--',
                        'result' => '--',
                        'updated_at' => '',
                    ];
                }else{
                    $live = [
                        'set' => $stopResult->set,
                        'val' => $stopResult->val,
                        'result' => $stopResult->number,
                        'updated_at' => $stopResult->created_at->format('Y-m-d H:i:s')//$item['date'],
                    ];
                }
            
            


            $thai = TwoDResult::where('country','Thai')
                                    ->orderBy('date','desc')
                                    ->orderBy('serial','asc')
                                    ->where('status', 1)
                                    ->limit(2)
                                    ->get();
            
          

            $data['status'] = $status;
            $data['total_users'] = 0;
            $data['live'] = $live;
            $data['thai'] = $thai;
           


            //Store in FireStore
            app('firebase.firestore')->database()->collection('live')->document('2d')->update([
                [
                    'path' => 'live', 
                    'value' => $data
                ]
            ]);

            return $data;

        }else if($holiday){

            $status = false;
            // Stop Live Data
            $stopResult = TwoDResult::where('time_type', 'PM')
                                    ->where('country', 'Thai')
                                    ->where('status', 1)
                                    ->orderBy('id','desc')
                                    ->first();

                if(!$stopResult){
                    $live = [
                        'set' => '--',
                        'val' => '--',
                        'result' => '--',
                        'updated_at' => '',
                    ];
                }else{
                    $live = [
                        'set' => $stopResult->set,
                        'val' => $stopResult->val,
                        'result' => $stopResult->number,
                        'updated_at' => $stopResult->created_at->format('Y-m-d H:i:s')//$item['date'],
                    ];
                }


            $thai = TwoDResult::where('country','Thai')
                                    ->orderBy('date','desc')
                                    ->orderBy('serial','asc')
                                    ->where('status', 1)
                                    ->limit(2)
                                    ->get();

            $data['status'] = $status;
            $data['total_users'] = 0;
            $data['live'] = $live;
            $data['thai'] = $thai;
          

            //Store in FireStore
            app('firebase.firestore')->database()->collection('live')->document('2d')->update([
                [
                    'path' => 'live', 
                    'value' => $data
                ]
            ]);

            return $data;
        

        //If not holiday or close day
        }else{  
            //Check if morning or evening
            if($currentTime > 0600 && $currentTime < 1202){
                $time_type = "AM";
                $status = true;
            }else if($currentTime > 1400 && $currentTime < 1630){
                $time_type = "PM";
                $status = true;
            }else{
                $status = false;
            }

            try{
                
                //Check if Current Time is Befor 9:30Am
                if($currentTime >= 1 && $currentTime <= 930){
                    $stopResult = TwoDResult::where('time_type', 'PM')
                                    ->where('date', Carbon::yesterday()->toDateString())
                                    ->where('country', 'Thai')
                                    ->first();

                    if(!$stopResult){
                        $live = [
                            'set' => '--',
                            'val' => '--',
                            'result' => '--',
                            'updated_at' => '',
                        ];
                    }else{
                        $live = [
                            'set' => $stopResult->set,
                            'val' => $stopResult->val,
                            'result' => $stopResult->number,
                            'updated_at' => $stopResult->created_at->format('Y-m-d H:i:s')//$item['date'],
                        ];
                    }
                
                //Check if Current time is After 4:30pm
                }else if($currentTime > 1630 && $currentTime <= 2359){
                    $stopResult = TwoDResult::where('time_type', 'PM')
                                    ->where('date', now()->toDateString())
                                    ->where('country', 'Thai')
                                    ->first();
                    
                    if(!$stopResult){
                        $live = [
                            'set' => '--',
                            'val' => '--',
                            'result' => '--',
                            'updated_at' => '',
                        ];
                    }else{
                        $live = [
                            'set' => $stopResult->set,
                            'val' => $stopResult->val,
                            'result' => $stopResult->number,
                            'updated_at' => $stopResult->created_at->format('Y-m-d H:i:s')//$item['date'],
                        ];
                    }
                
                //Check if Current time is Between 12:00pm and 2:00pm
                }else if($currentTime >= 1200 && $currentTime < 1400){
                    $stopResult = TwoDResult::where('time_type', 'AM')
                                        ->where('date', now()->toDateString())
                                        ->where('country', 'Thai')
                                        ->first();
                    if(!$stopResult){
                        $live = [
                            'set' => '--',
                            'val' => '--',
                            'result' => '--',
                            'updated_at' => ''
                        ];
                    }else{
                        $live = [
                            'set' => $stopResult->set,
                            'val' => $stopResult->val,
                            'result' => $stopResult->number,
                            'updated_at' => $stopResult->created_at->format('Y-m-d H:i:s')//$item['date'],
                        ];
                    }

                //Time to Show Live
                }else{
                    $url = 'https://api.thaistock2d.com/live';
                    $response = json_decode(file_get_contents($url));

                    $live = [
                        'set' => $response->live->set,
                        'val' => $response->live->value,
                        'result' => $response->live->twod,
                        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                    ];
                }

                

                //Thai Two-D
                $thai = TwoDResult::where('date', now()->toDateString())
                                    ->where('country','Thai')
                                    ->orderBy('time_type','asc')
                                    ->get();
                if(count($thai) < 1){
                    $thaiData = [
                        [
                            'number'    => "--",
                            'set'       => "--",
                            'val'       => "--",
                            'time_type' => "AM",
                            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ],
                        [
                            'number'    => "--",
                            'set'       => "--",
                            'val'       => "--",
                            'time_type' => "PM",
                            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ]
                    ];
                }else if(count($thai) > 1){
                    $thaiData = [
                        [
                            'number'    => $thai[0]->number ? $thai[0]->number : "--",
                            'set'       => $thai[0]->set ? $thai[0]->set : "--",
                            'val'       => $thai[0]->val ? $thai[0]->val : "--",
                            'time_type' => "AM",
                            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ],
                        [
                            'number'    => $thai[1]->number ? $thai[1]->number : "--",
                            'set'       => $thai[1]->set ? $thai[1]->set : "--",
                            'val'       => $thai[1]->val ? $thai[1]->val : "--",
                            'time_type' => "PM",
                            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ]
                    ];
                }else{
                    $thaiData = [
                        [
                            'number'    => $thai[0]->number ? $thai[0]->number : "--",
                            'set'       => $thai[0]->set ? $thai[0]->set : "--",
                            'val'       => $thai[0]->val ? $thai[0]->val : "--",
                            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ],
                        [
                            'number'    => "--",
                            'set'       => "--",
                            'val'       => "--",
                            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ]
                    ];
                }
                    
               
                    
                // Live Data Check
                
                $data['status'] = $status;
                $data['live'] = $live;
                $data['thai'] = $thaiData;
              

                //Store in FireStore
                app('firebase.firestore')->database()->collection('live')->document('2d')->update([
                    [
                        'path' => 'live', 
                        'value' => $data
                    ]
                ]);

                return $data;


                }catch(\Exception $e){
                    return $e->getMessage();
                }
        }
   
    }
}
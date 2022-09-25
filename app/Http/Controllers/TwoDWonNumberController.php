<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Goutte;
use Carbon\Carbon;
use Log;

use App\Models\TwoDWonNumber;

class TwoDWonNumberController extends Controller
{

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        Log::info('2D Won Num Create');
        $crawler = Goutte::request('GET', 'https://classic.set.or.th/mkt/sectorialindices.do?language=en&country=US');
        $item['date'] = Str::replace('* Market data provided for educational purpose or personal use only, not intended for trading purpose. * Last Update ','',$crawler->filter('#maincontent .row .table-info caption')->text());
        $item['set'] = $crawler->filter('#maincontent .row .table-info tbody tr td')->eq(1)->text();
        $item['val'] = $crawler->filter('#maincontent .row .table-info tbody tr td')->eq(7)->text();
        $item['result'] = Str::substr($item['set'], -1) . Str::substr(Str::before($item['val'], '.'), -1);
        
        
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

        $toInsert = [
            'number'    => $item['result'],
            'set'       => $item['set'],
            'val'       => $item['val'],
            'time_type' => $time_type,
            'created_by'=> 1,
            'date'      => now()->toDateString(),
            'created_at'=> now(),
            'updated_at'=> now()
        ];

        TwoDWonNumber::insert($toInsert);
    }

    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

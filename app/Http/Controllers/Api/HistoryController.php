<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LotteryHistoryResource;
use App\Models\LotteryHistory;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class HistoryController extends Controller
{
    use ResponseTrait;

    private function fetchDataFromServer($url)
    {
        $response = file_get_contents($url);

        if ($response !== false) {
            return json_decode($response, true);
        } else {
            return null;
        }
    }

    public function getFDHistory()
    {
        try {
            $url = "https://admin.4dmyanthai.com/api/lottery/fdhistroy";
            $data = $this->fetchDataFromServer($url);

            if ($data !== null) {
                return $data;
            } else {
                return $this->fail("Failed to fetch data from the server", 500);
            }
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage() ?: "server error", 500);
        }
    }

    public function getTwDHistory()
    {
        try {
            $url = "https://admin.4dmyanthai.com/api/tdhistory";
            $data = $this->fetchDataFromServer($url);

            if ($data !== null) {
                return $data;
            } else {
                return $this->fail("Failed to fetch data from the server", 500);
            }
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage() ?: "server error", 500);
        }
    }

    public function getThreeDHistory()
    {
        try {
            $url = "https://admin.4dmyanthai.com/api/tedhistory";
            $data = $this->fetchDataFromServer($url);

            if ($data !== null) {
                return $data;
            } else {
                return $this->fail("Failed to fetch data from the server", 500);
            }
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage() ?: "server error", 500);
        }
    }



public function saveLotteryWinNumbersFromAPI(Request $request)
{
    try {
        Log::info('Request received to save lottery win numbers from API.');

        $response = Http::withHeaders([
            'X-RapidAPI-Key' => '4cc8752188msh04d1cae3de5f8dcp144099jsn60d6fc38592e',
            'X-RapidAPI-Host' => 'thai-lottery1.p.rapidapi.com',
        ])->get('https://thai-lottery1.p.rapidapi.com/', [
            'date' => '01102566', 
        ]);

        if ($response->failed()) {
            Log::error('Failed to request API. Status Code: ' . $response->status());
            return response()->json(['message' => 'Fail to request API'], 400);
        }

        if ($response->serverError()) {
            Log::error('Server error while requesting API. Status Code: ' . $response->status());
            return response()->json(['message' => 'Server error'], 500);
        }

        $result = json_decode($response->body(), true);
        Log::info('API response received successfully.', ['response' => $result]);

        $first = array_slice($result[0], 1, count($result[0]) - 1, true);
        $first_three_digit = array_slice($result[1], 1, count($result[1]) - 1, true);
        $last_three_digit = array_slice($result[2], 1, count($result[2]) - 1, true);
        $last_two_digit = array_slice($result[3], 1, count($result[3]) - 1, true);
        $first_near = array_slice($result[4], 1, count($result[4]) - 1, true);
        $second = array_slice($result[5], 1, count($result[5]) - 1, true);
        $third = array_slice($result[6], 1, count($result[6]) - 1, true);
        $fourth = array_slice($result[7], 1, count($result[7]) - 1, true);
        $fifth = array_slice($result[8], 1, count($result[8]) - 1, true);

        Log::info('Parsed API response data.');

        $data = new LotteryHistory();
        $data->open_at = '2023-10-01';
        $data->year = '2023';
        $data->month = '10';
        $data->first = implode($first);
        $data->first_three_digit = implode(',', $first_three_digit);
        $data->last_three_digit = implode(',', $last_three_digit);
        $data->last_two_digit = implode(',', $last_two_digit);
        $data->first_near = implode(',', $first_near);
        $data->second = implode(',', $second);
        $data->third = implode(',', $third);
        $data->fourth = implode(',', $fourth);
        $data->fifth = implode(',', $fifth);
        $data->save();

        Log::info('Lottery data saved successfully.', ['data' => $data]);
    } catch (\Exception $e) {
        Log::error('An error occurred while saving lottery win numbers from API.', ['exception' => $e]);
        return response()->json(['message' => 'An error occurred'], 500);
    }
}


      public function getLotteryWinNumbers(Request $request)
      {
        $month = $request->month;
        $year = $request->year;
        if(!$request->year){
            $year = Carbon::now()->year;
        }
        if(!$request->month){
            $month = Carbon::now()->month;
        }
        $data = LotteryHistory::when($year, function ($query) use ($year){
                                    return $query->where('year', $year);
                                })->when($month, function ($query) use ($month){
                                    return $query->where('month', $month);
                                })->orderBy('id','desc')->get();

        if ($data) {
            return response()->json([
                'status' => true,
                'message' => 'Success',
                'month' => (string)$month,
                'year' => (string)$year,
                'data' =>  LotteryHistoryResource::collection($data),
            ]);
        } else {
            return $this->fail('There is no data', 404);
        }
    }

}
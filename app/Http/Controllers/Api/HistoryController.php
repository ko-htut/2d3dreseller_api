<?php

namespace App\Http\Controllers\Api\User;

use App\Models\TicketLedger;
use App\Models\TicketResult;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ResponseTrait;

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
            $url = "https://moez2d3d.com/api/lottery/fdhistroy";
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
            $url = "https://moez2d3d.com/api/tdhistory";
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
            $url = "https://moez2d3d.com/api/tdhistory";
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
}
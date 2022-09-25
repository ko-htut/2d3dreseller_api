<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Goutte;
use Log;

use App\Models\TwoDWonNumber;
use App\Models\TwoDChangeNumber;

class WebSocketController implements MessageComponentInterface
{
    protected $clients;
    private $subscriptions;
    private $users;
    private $userresources;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->subscriptions = [];
        $this->users = [];
        $this->userresources = [];
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        $this->users[$conn->resourceId] = $conn;
    }

    public function onMessage(ConnectionInterface $conn, $msg)
    {
        $data = json_decode($msg);
        if (isset($data->command)) {
            switch ($data->command) {
                case 'twod-live':

                    $crawler = Goutte::request('GET', 'https://classic.set.or.th/mkt/sectorialindices.do?language=en&country=US');
                    $item['date'] = Str::replace('* Market data provided for educational purpose or personal use only, not intended for trading purpose. * Last Update ','',$crawler->filter('#maincontent .row .table-info caption')->text());
                    $item['set'] = $crawler->filter('#maincontent .row .table-info tbody tr td')->eq(1)->text();
                    $item['val'] = $crawler->filter('#maincontent .row .table-info tbody tr td')->eq(7)->text();
                    $item['result'] = Str::substr($item['set'], -1) . Str::substr(Str::before($item['val'], '.'), -1);

                    $wonNumber = TwoDWonNumber::whereDate('date', now()->toDateString())
                                    ->select('number', 'set', 'val', 'time_type', 'date')
                                    ->get();
                    
                    $time_type = null;
                    if($currentHour > "6" && $currentHour < "12" ||
                    $currentHour == "6" && $currentMin >= "00" ||
                    $currentHour == "12" && $currentMin < "02"
                    ){
                        $time_type = "AM";
                    }else{
                        $time_type = "PM";
                    }

                    $lastNumber = TwoDChangeNumber::orderBy('id','desc')->first();
                    if($lastNumber->number != $item['result']){
                        $lastNumber->number = $item['result'];
                        $lastNumber->save();
                    }

                    $changeNumber = TwoDChangeNumber::whereDate('date', now()->toDateString())
                                        ->where('time_type', $time_type)
                                        ->select('number')->get();

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
                    $conn->send(json_encode($result));

                    break;
                default:
                    break;
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
        unset($this->users[$conn->resourceId]);
        unset($this->subscriptions[$conn->resourceId]);

        foreach ($this->userresources as &$userId) {
            foreach ($userId as $key => $resourceId) {
                if ($resourceId==$conn->resourceId) {
                    unset( $userId[ $key ] );
                }
            }
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }

}

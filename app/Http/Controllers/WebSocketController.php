<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Goutte;
use Log;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;

use App\Models\TwoDWonNumber;
use App\Models\TwoDChangeNumber;

class WebSocketController implements MessageComponentInterface
{
    protected $clients;
    private $subscriptions;
    private $users;
    private $userresources;
    private $connetedUser;

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
        echo $msg;
        $data = json_decode($msg);
        if (isset($data->command)) {
            switch ($data->command) {
                case "subscribe":
                    $this->subscriptions[$conn->resourceId] = $data->channel;
                break;
                default:
                    // $example = array(
                    //     'methods' => [
                    //                 "subscribe" => '{command: "subscribe", channel: "global"}',
                    //                 "groupchat" => '{command: "groupchat", message: "hello glob", channel: "global"}',
                    //                 "message" => '{command: "message", to: "1", message: "it needs xss protection"}',
                    //                 "register" => '{command: "register", userId: 9}',
                    //             ],
                    // );
                    // $conn->send(json_encode($example));
                    break;
            }

            $dw = date("w");
            $status = true;
            if($dw == 0 || $dw == 6){
                $status = false;
            }else{
                $status = true;
            }

            if($status){
                $currentHour = Carbon::now()->format('H');
                $currentMin = Carbon::now()->format('i');
                if($currentHour < '12' && $currentMin < '10'){
                    $message = '{message:"2D Stock is pending to open"}';
                    $conn->send(json_encode($message));
                }else{
                    $loop = Loop::get();
                    $counter = 0;
                    $loop->addPeriodicTimer(1, function() use($conn, &$counter){
                        $counter++;
        
                        if($counter === 10){
                            sleep(3);
                            $counter = 0;
                        }
                        $result = app('App\Http\Controllers\TwoDLiveController')->update();
                        $conn->send(json_encode($result));
                    });
                    
                }
                
            }else{
                $message = '{message:"2D Stock is close today"}';
                $conn->send(json_encode($message));
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

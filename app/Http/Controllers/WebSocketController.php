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
                case "twod":
                    $this->live2D();
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

    public function live2D(ConnectionInterface $conn)
    {
        $dw = date("w");
        $status = true;
        if($dw == 0 || $dw == 6){
            $status = false;
        }else{
            $status = true;
        }

        if($status){
            $loop = Loop::get();
                $counter = 0;
                $loop->addPeriodicTimer(1, function() use($conn, &$counter){
                $result = app('App\Http\Controllers\TwoDLiveController')->update();
                $conn->send(json_encode($result));
            });
            
        }else{
            $message = '{message:"2D Stock is close today"}';
            $conn->send(json_encode($message));
        }
    }

}

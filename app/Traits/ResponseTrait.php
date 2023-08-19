<?php

namespace App\Traits;

trait ResponseTrait
{
    public function success($message,$data=[],$status=200){
        return response([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    protected function fail($message, $status = 422)
    {
        return response([
            'success' => false,
            'message' => $message,
        ], $status);
    }
}

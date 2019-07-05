<?php
namespace App\Traits;

trait CustomResponse
{
    public function CustomResponse($code, $message, $data = null, $paginate = false){

        return response()->make([
            'status' => $code,
            'message' => $message,
            'data' => $data
        ], $code);
    }

}

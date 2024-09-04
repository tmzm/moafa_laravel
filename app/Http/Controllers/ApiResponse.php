<?php

namespace App\Http\Controllers;

trait ApiResponse
{
    public function apiResponse($status = null,$msg = null,$data = null,$token = null,$errors = null)
    {
        $array = [];

        if ($msg != null) {
            $array['message'] = $msg;
        }

        if ($data != null || is_object($data)) {
            $array['data'] = $data;
        }

        if ($token != null) {
            $array['token'] = $token;
        }

        if ($errors != null) {
            $array['errors'] = $errors;
        }

        return response()->json($array,$status);
    }
}

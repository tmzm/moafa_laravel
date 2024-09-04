<?php

namespace App\Http\Helpers;

use App\Enums\ReturnMessages;
use App\Http\Controllers\ApiResponse;
use Illuminate\Http\Exceptions\HttpResponseException;

trait MessageHelper
{
    use ApiResponse;

    public function ok($data = null, $token = null)
    {
        throw new HttpResponseException(self::apiResponse(200,ReturnMessages::Ok->value,$data,$token));
    }

    public function unHandledError($error = null)
    {
        throw new HttpResponseException(self::apiResponse(400,$error === null ? ReturnMessages::Error->value : $error));
    }

    public function unAuth()
    {
        throw new HttpResponseException(self::apiResponse(401,ReturnMessages::UnAuth->value));
    }

    public function notFound()
    {
        throw new HttpResponseException(self::apiResponse(404,ReturnMessages::NotFound->value));
    }
}

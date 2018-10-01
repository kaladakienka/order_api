<?php declare(strict_types = 1);

namespace App\Http\Controllers;

class BaseController
{
    public function sendResponse($data, $httpCode)
    {
        return response()->json($data, $httpCode);
    }
}
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

class PingController extends Controller
{
    public function __invoke()
    {
        return response()->json([
            'ok' => true,
        ]);
    }
}

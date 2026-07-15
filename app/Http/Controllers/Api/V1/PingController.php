<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PingController extends Controller
{
    public function __invoke()
    {
        return response()->json([
            'ok' => true
        ]);
    }
}

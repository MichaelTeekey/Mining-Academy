<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Routing\Controller as Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    //
    public function me(Request $request)
    {
        return response()->json($request->user()->load('organization'));
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Routing\Controller as Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    //
    public function me(Request $request)
    {
        try {
            return response()->json($request->user()->load('organization'));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve user profile'], 500);
        }
    }
}

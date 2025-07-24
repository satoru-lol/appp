<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller; 

class ExternalController extends Controller
{
    public function getUsers(Request $request)
    {

        try {
             $users = User::where('psy_lance', '!=', null)->get();

            return response()->json([
                'users' => $users->toArray()
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FirebaseController extends Controller
{
    public function updateToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string|min:10'
        ]);
        
        auth()->user()->update(['fcm_token' => $request->fcm_token]);
        
        return response()->json([
            'success' => true,
            'message' => 'تم تحديث التوكن'
        ]);
    }
    
    public function validateToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string'
        ]);
        
        $user = auth()->user();
        $isValid = $user && $user->fcm_token === $request->token;
        
        return response()->json([
            'valid' => $isValid
        ]);
    }
}

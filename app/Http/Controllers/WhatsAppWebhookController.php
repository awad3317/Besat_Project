<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class WhatsAppWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $event = $request->input('event'); 
        $data = $request->input('data');

        Log::info("WhatsApp Webhook Received: {$event}");

        if ($event === 'connection.update') {
            $status = $data['state'] ?? 'DISCONNECTED'; 
            Cache::put('whatsapp_status', $status, now()->addDays(7));
        }

        return response()->json(['status' => 'success'], 200);
    }
}
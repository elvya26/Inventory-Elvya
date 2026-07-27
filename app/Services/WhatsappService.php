<?php

namespace App\Services;

use App\Models\NotificationMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    public function sendMessage(string $phone, string $message): bool
    {
        // 1. Clean the phone number
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($cleanPhone, '08')) {
            $cleanPhone = '628' . substr($cleanPhone, 2);
        }

        // 2. Log message to laravel.log
        Log::info("WhatsApp Auto-Sent to {$cleanPhone}: {$message}");

        // 3. Create record in NotificationMessage table as 'sent'
        try {
            NotificationMessage::create([
                'title' => 'WA Notifikasi Otomatis',
                'recipient' => $cleanPhone,
                'channel' => 'whatsapp',
                'message' => $message,
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error("Failed to save WA notification to DB: " . $e->getMessage());
        }

        // 4. If Fonnte API Gateway Token is set, send a real WhatsApp message in the background
        $fonnteToken = env('FONNTE_TOKEN');
        if ($fonnteToken) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => $fonnteToken,
                ])->post('https://api.fonnte.com/send', [
                    'target' => $cleanPhone,
                    'message' => $message,
                ]);

                return $response->successful();
            } catch (\Throwable $e) {
                Log::error("Real WhatsApp Gateway transmission failed: " . $e->getMessage());
            }
        }

        return true;
    }
}

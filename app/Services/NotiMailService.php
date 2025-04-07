<?php

namespace App\Services;

use App\Mail\NotiMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotiMailService
{
    // this is the common method to send noti mail
    // LOG for debugging purpose
    public static function sendNotiMail($toEmail, $subject, $message)
    {
        try {
            $data = [
                'subject' => $subject,
                'message' => $message
            ];
            Log::info("Sending email noti to : " . $toEmail);
            Mail::to($toEmail)->send(new NotiMail($data));
            Log::info("Email Noti sent to : ". $toEmail);
        } catch (\Exception $e) {
            \Log::error("Email Noti not sent: " . $e->getMessage());
        }
    }
}

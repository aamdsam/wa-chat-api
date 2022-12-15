<?php

use App\Models\ChatLog;
use App\Models\LogActivity;
use App\Models\MessageTemplates;
use Carbon\Carbon;
use Illuminate\Support\Facades\Request;

if (!function_exists('log_activity')) {
    function log_activity($title, $description ='')
    {
        LogActivity::create([
            'title' => $title,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'expired_at' => Carbon::now()->addMinutes(10),
        ]);
    }
}

if (!function_exists('get_template')) {
    function get_template($msg)
    {
        $messageTemplates = MessageTemplates::where('template',$msg)->first('message');
        if (!$messageTemplates){
            return [
                'status' => 'error',
                'data' => 'data not found',
            ];
        }

        return [
            'status' => 'success',
            'data' => $messageTemplates,
        ];
    }
}

if (!function_exists('SetChatLog')) {
    function SetChatLog($from, $type,$input, $output, $detail)
    {
        ChatLog::create([
            'message_from' => $from,
            'message_type' => $type,
            'message_input' => $input,
            'message_output' => $output,
            'message_detail' => $detail,
            'expired_at' => Carbon::now()->addMinutes(10),
        ]);
    }
}

if (!function_exists('getChatLog')) {
    function getChatLog($from, $type,$input, $output, $detail)
    {
        ChatLog::create([
            'message_from' => $from,
            'message_type' => $type,
            'message_input' => $input,
            'message_output' => $output,
            'message_detail' => $detail,
            'expired_at' => Carbon::now()->addMinutes(10),
        ]);
    }
}
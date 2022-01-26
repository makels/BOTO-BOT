<?php
namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;

class TelegramController extends Controller
{

    public function webhook(Request $request)
    {
        $key = env('TG_KEY');

        $telegram = new Api($key);

        $result = $telegram->getWebhookUpdate();

        $text       = Setting::get(Setting::TELEGRAM_HEADER)->value;
        $chat_id    = isset($result["message"]["chat"]["id"]) ? $result["message"]["chat"]["id"] : 0;
        $username   = isset($result["message"]["from"]["username"]) ? $result["message"]["from"]["username"] : "";

        Log::info($text);
        
        $telegram->sendMessage([
            'chat_id'       => $chat_id,
            'text'          => $text
        ]);
    }
}

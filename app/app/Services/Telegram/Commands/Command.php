<?php

namespace App\Services\Telegram\Commands;

use App\Services\Telegram\TelegramAPI;
use Illuminate\Http\Request;

class Command extends TelegramAPI
{

    public $message;

    public function __construct(Request $request, $back = false)
    {
        parent::__construct($request);
        if ($back) {
            $this->chat_id    = $request->input('callback_query.message.chat.id');
            $this->message_id = $request->input('callback_query.message.message_id');
        } else {
            $this->chat_id    = $request->input('message.chat.id');
            $this->message_id = $request->input('message.message_id');
            $this->message    = $request->input('message.text');
        }
    }

}
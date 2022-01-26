<?php

namespace App\Services\Telegram\Callback;


use Illuminate\Http\Request;

class Delete extends CallbackQuery
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        parent::deleteMessage();
    }
}
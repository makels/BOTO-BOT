<?php

namespace App\Services\Telegram\Callback;


use Illuminate\Http\Request;

class OnlinePay extends CallbackQuery
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        parent::sendServiceInvoice();
    }
}

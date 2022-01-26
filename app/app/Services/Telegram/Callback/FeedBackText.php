<?php

namespace App\Services\Telegram\Callback;


use Illuminate\Http\Request;

class FeedBackText extends CallbackQuery
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        parent::setStarts();
        parent::setData(['FeedBack' => 1]);
        parent::editMessage('Напишите, что Вам не понравилось?');
    }
}
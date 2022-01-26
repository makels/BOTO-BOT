<?php

namespace App\Services\Telegram\Callback;

use Illuminate\Http\Request;

class PersonalBalance extends CallbackQuery
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->back = 'Personal_';
        is_null($this->user->bonus) ? $bonus = 0 : $bonus = $this->user->bonus;
        parent::editMessage(__('Ваш баланс:').' '.$bonus.' '.__('баллов.'), parent::buildInlineKeyboard());
    }
}
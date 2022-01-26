<?php

namespace App\Services\Telegram\Callback;

use Illuminate\Http\Request;

class PersonalRecordEdit extends CallbackQuery
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $record_id = parent::getCallbackID();
        $this->back = 'PersonalRecord_'.$record_id;

        parent::editMessage(
            __('Редактировать:'),
            parent::buildInlineKeyboard([
//                [['text' => __('Дату'), 'callback_data' => '-']],
                [['text' => __('Время'), 'callback_data' => 'PersonalRecordEditTime_'.$record_id]]
            ])
        );
    }
}
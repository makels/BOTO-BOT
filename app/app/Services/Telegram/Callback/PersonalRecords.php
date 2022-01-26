<?php

namespace App\Services\Telegram\Callback;

use Carbon\Carbon;
use Illuminate\Http\Request;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class PersonalRecords extends CallbackQuery
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->back = 'Personal_';
        parent::editMessage(__('Мои записи'), $this->getRecords());
    }

    private function getRecords (): InlineKeyboardMarkup
    {
        if (!isset($this->user->records) || $this->user->records->isEmpty())
            return parent::buildInlineKeyboard();

        $records = [];
        foreach ($this->user->records as $k => $record)
            if (
                Carbon::parse($record->date)->greaterThan(Carbon::now())
            )
                $records[] = [['text' => __('Запись #').($k+1), 'callback_data' => 'PersonalRecord_'.$record->id]];

        return parent::buildInlineKeyboard($records);
    }
}

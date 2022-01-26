<?php

namespace App\Services\Telegram\Callback;

use App\Models\Record;
use Carbon\Carbon;
use Illuminate\Http\Request;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class PersonalRecord extends CallbackQuery
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $record = Record::find(parent::getCallbackValue());
        if (!$record)
            return;

        $this->back = 'PersonalRecords_';
        parent::editMessage($this->getText($record), $this->getButtons($record));
    }

    private function getText($record): string
    {
        if (!is_null($record->user_id))
            return
                'Запись #'.$record->id."\n".
                '1. Услуга: '.$record->service->name."\n".
                '2. Адрес: '.$record->address->address."\n".
                '3. Специалист: '.$record->user->name."\n".
                '4. Дата: '.$record->date."\n".
                '5. Время: '.$record->time."\n".
                '6. Стоимость: '.$record->service->price.' грн';
        return
            'Запись #'.$record->id."\n".
            '1. Услуга: '.$record->service->name."\n".
            '2. Адрес: '.$record->address->address."\n".
            '3. Дата: '.$record->date."\n".
            '4. Время: '.$record->time."\n".
            '5. Стоимость: '.$record->service->price.' грн';
    }

    private function getButtons ($record): InlineKeyboardMarkup
    {

        if (Carbon::parse($record->date) < Carbon::now()->format('Y-m-d') || !Carbon::parse($record->date.' '.$record->time)->greaterThan(Carbon::now()))
            return parent::buildInlineKeyboard();

        $buttons[] = [['text' => 'Удалить','callback_data' => 'PersonalRecordDelete_'.$record->id]];
        $buttons[] = [['text' => 'Редактировать','callback_data' => 'PersonalRecordEdit_'.$record->id]];

        return parent::buildInlineKeyboard($buttons);

    }
}

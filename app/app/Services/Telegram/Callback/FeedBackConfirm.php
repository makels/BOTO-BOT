<?php

namespace App\Services\Telegram\Callback;


use Illuminate\Http\Request;

class FeedBackConfirm extends CallbackQuery
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $record = \App\Models\Record::find(parent::getRecordID());
        if (!$record)
            return false;

        parent::resetData();

        $feedback = \App\Models\FeedBack::create(
            [
                'telegram_user_id' => $this->user->id,
                'service_id' => $record->service_id,
                'address_id' => $record->address_id ?? null,
                'user_id' => $record->user_id ?? null,
                'stars' => parent::setStarts(),
            ]
        );

        if ($feedback)
            return parent::editMessage('Спасибо за Ваш отзыв!');

        return false;
    }
}
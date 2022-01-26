<?php

namespace App\Services\Telegram\Callback;

use App\Models\Record;
use Illuminate\Http\Request;

class PersonalRecordEditTime extends CallbackQuery
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $record = Record::find(parent::setRecordID());
        $this->back = 'PersonalRecordEdit_'.parent::getRecordID();

        if ( !is_null($record->user_id) && !empty($record->user_id) ) {
            $buttons = $this->masterTime($record->user_id, $record->service_id, $record->address_id, $record->date);
        } else {
            $buttons = $this->serviceTime($record->service_id, $record->date);
        }
        return parent::editMessage(__('Выберете время'), $buttons);
    }

    private function serviceTime (int $service_id, string $date)
    {
        $service = \App\Models\Service::find($service_id);
        $times = $service->timetable->getFreeTimes($date);
        return $this->getButtons($times);
    }

    private function masterTime(int $master_id, int $service_id, int $address_id, string $date)
    {
        $master = \App\Models\User::find($master_id);
        $times = \App\Models\UserTimetable::getFreeTimes($master, $address_id, $service_id, $date);
        return $this->getButtons($times);
    }

    private function getButtons ($times)
    {
        $buttons = [];
        if (empty($times))
            $buttons[] = [['text' => __('Нет свободных ячеек.'), 'callback_data' => '-']];
        else
            foreach ($times as $time)
                $buttons[] = [['text' => $time, 'callback_data' => 'PersonalEditConfirm_' . $time]];

        return parent::buildInlineKeyboard($buttons);
    }
}

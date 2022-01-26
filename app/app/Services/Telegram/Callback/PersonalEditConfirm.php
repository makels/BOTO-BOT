<?php

namespace App\Services\Telegram\Callback;


use App\Models\Record;
use Illuminate\Http\Request;

class PersonalEditConfirm extends CallbackQuery
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $record = Record::find(parent::getRecordID());
        $record->time = parent::setTime();
        $record->date = parent::getDate();
        $record->save();

        $address_id = $record->address_id;
        $user_id = $record->user_id;
        $service_name = $record->service->name;
        $date = $record->date;
        $time = $record->time;

        parent::editMessage(__('Запись изменена.'));

        $fio = $this->user->getFio();
        if (!\ConnectService::prepareJob())
            return false;


        $notice_mess = __('Клиент').' <b>'.$fio.'</b> '.__('перенес запись на услугу').'"'.$service_name.'" на '.$date. ' '.$time;
        \App\Jobs\SendNotice::dispatch(
            $this->business_db,
            [
                [
                    'address_id' => $address_id,
                    'message' => $notice_mess
                ],
                [
                    'user_id' => $user_id,
                    'message' => $notice_mess
                ]
            ],
            )->delay(now()->addMinutes(2));

        return true;
    }
}

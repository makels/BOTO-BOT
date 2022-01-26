<?php

namespace App\Services\Telegram\Callback;

use Illuminate\Http\Request;

class BonusPay extends CallbackQuery
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $service = \App\Models\Service::find(parent::getServiceID());
        if ($this->user->bonus >= $service->price) {
            $record = $this->createRecord(true, false, $service->price);
            if ($record) {
                parent::deleteMessage();
                parent::getMenu(__('Спасибо! Активные записи и количество бонусов доступны в <b>личном кабинете</b>.'));
            }
        } else {
            parent::sendMessage(__('Недостаточно бонусов.'));
        }
    }
}
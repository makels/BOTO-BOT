<?php

namespace App\Services\Telegram\Callback;


use Illuminate\Http\Request;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class Address extends CallbackQuery
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $service_id = parent::setServiceID();
        $this->back = 'Service_'.parent::getTypeID();
        return $this->editMessage(__('Выберете адрес'), $this->getAddress($service_id));
    }

    /**
     * @param $service_id
     * @return InlineKeyboardMarkup
     */
    private function getAddress($service_id): InlineKeyboardMarkup
    {
        $service = \App\Models\Service::find($service_id);
        $addresses = [];
        foreach ($service->addresses as $address) {
            if (empty($service->timetable))
                $addresses[] = [['text' => $address->address, 'callback_data' => 'Master_'.$address->id]];
            else
                $addresses[] = [['text' => $address->address, 'callback_data' => 'DatesService_'.$address->id]];
        }
        return parent::buildInlineKeyboard($addresses);
    }

}

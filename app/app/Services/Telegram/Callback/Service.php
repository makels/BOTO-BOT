<?php

namespace App\Services\Telegram\Callback;


use Illuminate\Http\Request;

class Service extends CallbackQuery
{
    /**
     * Service constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->back = 'TypesServices_';
        $type_id = parent::setTypeID();
        return $this->editMessage(__('Выберете услугу для записи'), $this->getServices($type_id));
    }

    /**
     * @param $type_id
     * @return \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup
     */
    public function getServices($type_id)
    {
        $type = \App\Models\TypeService::find($type_id);
        $array = [];
        foreach ($type->services as $service) {
            $array[] = [['text' => $service->name, 'callback_data' => 'Address_'.$service->id]];
        }
        return parent::buildInlineKeyboard($array);
    }
}

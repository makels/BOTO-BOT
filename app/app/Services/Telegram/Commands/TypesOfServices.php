<?php

namespace App\Services\Telegram\Commands;

use App\Services\Telegram\Callback\Service;
use Illuminate\Http\Request;
use App\Models\TypeService;

class TypesOfServices extends Command
{
    /**
     * TypesService constructor.
     * @param Request $request
     * @param bool $back
     * @throws \TelegramBot\Api\Exception
     * @throws \TelegramBot\Api\InvalidArgumentException
     */
    public function __construct(Request $request, bool $back = false)
    {
        parent::__construct($request, $back);
        return $this->sendMessage(__('Выберете тип услуги для записи'), $this->getServices());
    }

    /**
     * @return \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup
     */
    public function getServices()
    {
        $types = TypeService::all();
        $array = [];
        foreach ($types as $type) {
            if(\App\Models\Service::where('type_service_id', $type->id)->count() > 0) {
                $array[] = [['text' => $type->type, 'callback_data' => 'Service_'.$type->id]];
            }
        }
        return parent::buildInlineKeyboard($array);
    }
}

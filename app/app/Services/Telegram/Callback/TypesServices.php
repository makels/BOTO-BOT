<?php

namespace App\Services\Telegram\Callback;


use Illuminate\Http\Request;

class TypesServices extends CallbackQuery
{
    /**
     * TypesServices constructor.
     * @param Request $request
     * @throws \TelegramBot\Api\Exception
     * @throws \TelegramBot\Api\InvalidArgumentException
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->deleteMessage();
        new \App\Services\Telegram\Commands\TypesOfServices($request, true);
    }
}
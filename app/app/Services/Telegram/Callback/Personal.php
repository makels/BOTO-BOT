<?php

namespace App\Services\Telegram\Callback;

use Illuminate\Http\Request;
use TelegramBot\Api\Exception;
use TelegramBot\Api\InvalidArgumentException;

class Personal extends CallbackQuery
{
    /**
     * Personal constructor.
     * @param Request $request
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->deleteMessage();
        new \App\Services\Telegram\Commands\Personal($request, true);
    }
}

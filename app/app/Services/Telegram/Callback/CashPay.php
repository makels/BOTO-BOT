<?php

namespace App\Services\Telegram\Callback;

use Illuminate\Http\Request;
use TelegramBot\Api\Exception;
use TelegramBot\Api\InvalidArgumentException;

class CashPay extends CallbackQuery
{
    /**
     * CashPay constructor.
     * @param Request $request
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $record = $this->createRecord();
        if ($record) {
            parent::deleteMessage();
            parent::getMenu(__('Спасибо! Активные записи доступны в <b>личном кабинете</b>.'));
        }
    }
}

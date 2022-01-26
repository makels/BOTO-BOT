<?php


namespace App\Services\Telegram\Callback;


use Illuminate\Http\Request;
use TelegramBot\Api\Exception;
use TelegramBot\Api\InvalidArgumentException;

class PrePay extends CallbackQuery
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
            parent::getMenu(
                $record->service->prepayment->message."\n".
                __('Номер карты:').' '.$record->service->prepayment->card_number."\n".
                __('Активные записи доступны в <b>личном кабинете</b>.')

            );
        }
    }
}

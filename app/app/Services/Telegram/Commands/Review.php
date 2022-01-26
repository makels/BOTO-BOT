<?php

namespace App\Services\Telegram\Commands;

use Illuminate\Http\Request;

class Review extends Command
{
    /**
     * Review constructor.
     * @param Request $request
     * @param bool $back
     * @throws \TelegramBot\Api\Exception
     * @throws \TelegramBot\Api\InvalidArgumentException
     */
    public function __construct(Request $request, $back = false)
    {
        parent::__construct($request, $back);
        parent::setData(['Review' => 1]);
        return $this->sendMessage(__('Напишите Ваш отзыв:'));
    }
}
<?php

namespace App\Services\Telegram\Commands;


use App\Models\Record;
use Illuminate\Http\Request;

class Text extends Command
{

    /**
     * Text constructor.
     * @param Request $request
     * @throws \TelegramBot\Api\Exception
     * @throws \TelegramBot\Api\InvalidArgumentException
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $data = $this->getData();
        if (!$data)
            return false;
        return $this->getCallback($data);
    }

    /**
     * @param $data
     * @return bool|\TelegramBot\Api\Types\Message
     * @throws \TelegramBot\Api\Exception
     * @throws \TelegramBot\Api\InvalidArgumentException
     */
    private function getCallback($data)
    {
        if (isset($data->Review)) {
            $data->Review = $this->message;
            parent::setData($data);
            return $this->sendMessage(__('Ваша оценка:'), parent::buildStars());
        }
        elseif (isset($data->FeedBack)) {
            $record = Record::find(parent::getRecordID());
            if (!$record)
                return false;
            $feedback = \App\Models\FeedBack::create(
                [
                    'telegram_user_id' => $this->user->id,
                    'service_id' => $record->service_id,
                    'address_id' => $record->address_id,
                    'user_id' => $record->user_id ?? null,
                    'stars' => parent::getStars(),
                    'text' => $this->message
                ]
            );
            if ($feedback) {
                parent::resetData();
                return parent::sendMessage('Спасибо за Ваш отзыв!');
            }
        }
        return false;
    }
}
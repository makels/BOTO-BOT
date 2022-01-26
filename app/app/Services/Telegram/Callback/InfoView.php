<?php

namespace App\Services\Telegram\Callback;

use App\Models\Information;
use Illuminate\Http\Request;
use TelegramBot\Api\Exception;
use TelegramBot\Api\InvalidArgumentException;

class InfoView extends CallbackQuery
{
    /**
     * ShareView constructor.
     * @param Request $request
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->back = 'AboutUs_';
        $this->view();
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    private function view() {
        $info = Information::find($this->getCallbackID());

        if (!$info)
            return;

        if (!is_null($info->addresses)) {
            $addrs = json_decode($info->addresses);
            foreach ($addrs as $addr)
                $more[] = [['text' => $addr, 'url' => 'https://www.google.com/maps/search/?api=1&query='.str_replace(" ","+", $addr)]];
        }

        if (!is_null($info->button))
            $more[] = [['text' => 'Подробнее', 'url' => $info->button]];

        if (isset($more))
            $keyboard = parent::buildInlineKeyboard($more);
        else
            $keyboard = parent::buildInlineKeyboard();

        $mess = '<b>'.$info->title.'</b>'."\n\n".$info->text;

        if (is_null($info->img)) parent::editMessage($mess, $keyboard);
        else  {
            parent::deleteMessage();
            parent::sendPhoto(asset('public/storage/'.$info->img), $mess, $keyboard);
        }
    }
}

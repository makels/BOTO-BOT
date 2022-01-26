<?php

namespace App\Services\Telegram\Callback;

use App\Models\Share;
use Illuminate\Http\Request;
use TelegramBot\Api\Exception;
use TelegramBot\Api\InvalidArgumentException;

class ShareView extends CallbackQuery
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
        $this->back = 'Delete_';
        $this->view();
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    private function view() {
        $share = Share::find($this->getCallbackID());

        if (!$share)
            return;

        if (!is_null($share->button))
            $keyboard = parent::buildInlineKeyboard([[['text' => 'Подробнее', 'url' => $share->button]]]);
        else
            $keyboard = parent::buildInlineKeyboard();

        $mess = '<b>'.$share->title.'</b>'."\n\n".$share->text;

        if (is_null($share->img)) parent::sendMessage($mess, $keyboard);
        else  parent::sendPhoto(asset('public/storage/'.$share->img), $mess, $keyboard);
    }
}

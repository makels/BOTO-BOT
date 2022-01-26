<?php

namespace App\Services\Telegram\Commands;


use App\Models\Share;
use Illuminate\Http\Request;

class Shares extends Command
{
    /**
     * Shares constructor.
     * @param Request $request
     * @param bool $back
     * @throws \TelegramBot\Api\Exception
     * @throws \TelegramBot\Api\InvalidArgumentException
     */
    public function __construct(Request $request, bool $back = false)
    {
        parent::__construct($request, $back);
        parent::sendMessage('Выбирите Акции', $this->getShares());
    }

    /**
     * @return \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup
     */
    private function getShares()
    {
        $shares = Share::all();
        $array = [];
        foreach ($shares as $share) {
            $array[] = [['text' => $share->title, 'callback_data' => 'ShareView_'.$share->id]];
        }
        $array[] = [['text' => '< НАЗАД', 'callback_data' => 'Delete_']];
        return parent::buildInlineKeyboard($array);
    }
}
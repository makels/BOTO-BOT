<?php

namespace App\Services\Telegram\Callback;

use App\Models\TypeService;
use App\Services\Telegram\TelegramAPI;
use App\Models\TelegramSession;
use Carbon\Carbon;
use Illuminate\Http\Request;
use ConnectService;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class CallbackQuery extends TelegramAPI
{

    protected $data;
    protected $back;

    /**
     * CallbackQuery constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->data       = $request->input('callback_query.data');
        $this->chat_id    = $request->input('callback_query.message.chat.id');
        $this->message_id = $request->input('callback_query.message.message_id');
    }

    /**
     * @return int
     */
    protected function setTypeID ()
    {
        $id = $this->getID();
        if (isset($this->user->telegramSession)) {
            $this->user->telegramSession->type = $id;
            $this->user->telegramSession->save();
        } else {
            $session = new TelegramSession();
            $session->type = $id;
            $this->user->telegramSession()->save($session);
        }

        return $id;
    }

    /**
     * @return int
     */
    protected function setServiceID ()
    {
        $id = $this->getID();
        if (isset($this->user->telegramSession)) {
            $this->user->telegramSession->service = $id;
            $this->user->telegramSession->save();
        } else {
            $session = new TelegramSession();
            $session->service = $id;
            $this->user->telegramSession()->save($session);
        }

        return $id;
    }

    /**
     * @return int
     */
    protected function setAddressID ()
    {
        $id = $this->getID();
        if (isset($this->user->telegramSession)) {
            $this->user->telegramSession->address = $id;
            $this->user->telegramSession->save();
        } else {
            $session = new TelegramSession();
            $session->address = $id;
            $this->user->telegramSession()->save($session);
        }

        return $id;
    }

    /**
     * @param mixed $id
     * @return bool|int
     */
    protected function setMasterID ($id = true)
    {
        if ($id === true) $id = $this->getID();
        if ($this->user->telegramSession) {
            $this->user->telegramSession->master = $id;
            $this->user->telegramSession->save();
        } else {
            $session = new TelegramSession();
            $session->master = $id;
            $this->user->telegramSession()->save($session);
        }

        return $id;
    }

    /**
     * @return bool|string
     */
    protected function setDate ()
    {
        $date = $this->getString();
        if ($this->user->telegramSession) {
            $this->user->telegramSession->date = $date;
            $this->user->telegramSession->save();
        } else {
            $session = new TelegramSession();
            $session->date = $date;
            $this->user->telegramSession()->save($session);
        }

        return $date;
    }

    /**
     * @return bool|string
     */
    protected function setTime ()
    {
        $time = $this->getString();
        if ($this->user->telegramSession) {
            $this->user->telegramSession->time = $time;
            $this->user->telegramSession->save();
        } else {
            $session = new TelegramSession();
            $session->time = $time;
            $this->user->telegramSession()->save($session);
        }

        return $time;
    }

    /**
     * @return int
     */
    public function setStarts(): int
    {
        $stars = $this->getID();

        if ($this->user->telegramSession) {
            $this->user->telegramSession->stars = $stars;
            $this->user->telegramSession->save();
        } else {
            $session = new TelegramSession();
            $session->stars = $stars;
            $this->user->telegramSession()->save($session);
        }

        return $stars;
    }

    public function setRecordID(): int
    {
        $id = $this->getID();

        if ($this->user->telegramSession) {
            $this->user->telegramSession->record = $id;
            $this->user->telegramSession->save();
        } else {
            $session = new TelegramSession();
            $session->record = $id;
            $this->user->telegramSession()->save($session);
        }

        return $id;
    }

    /**
     * @return int
     */
    protected function getCallbackID (): int
    {
        return $this->getID();
    }

    /**
     * @return bool|string
     */
    protected function getClass ()
    {
        return substr($this->data, 0, strpos($this->data, '_', 1));
    }

    /**
     * @return int
     */
    private function getID (): int
    {
        return intval(substr($this->data, strpos($this->data, '_', 1)+1, 100));
    }

    /**
     * @return bool|string
     */
    private function getString ()
    {
        return substr($this->data, strpos($this->data, '_', 1)+1, 100);
    }

    /**
     * @param bool $int
     * @return bool|int|string
     */
    protected function getCallbackValue ($int = false)
    {
        if ($int) return $this->getID();
        return $this->getString();
    }

    /**
     * @param int $bonus
     */
    protected function sendServiceInvoice($bonus = 0)
    {
        $record_id = 0;
        try {
            $service = \App\Models\Service::find( $this->getServiceID() );
        } catch (\Exception $e) {
            return;
        }
        $price = intval( $service->price );
        if ($bonus > 0) $price = $this->currentPrice($price, $bonus);

        if (is_null($this->getMasterID()))
            $description = 'Дата: '.\Jenssegers\Date\Date::parse( $this->getDate() )->format('l j F Y')."\n". 'Время: '.$this->getTime()."\n";
        else
            $description = 'Специалист: '.\App\Models\User::find( $this->getMasterID() )->name."\n". 'Дата: '.\Jenssegers\Date\Date::parse( $this->getDate() )->format('l j F Y')."\n". 'Время: '.$this->getTime()."\n";

        $this->bot->sendInvoice(
            $this->chat_id,
            $service->name,
            $description,
            json_encode([
                "handler" => 'OnlinePayRecord',
                "service_id" => $service->id,
            ]),
            $this->pay_token,
            $this->chat_id,
            'UAH',
            [
                ['label' => $service->name, 'amount' => $price * 100],
            ],
            false
        );
    }

    /**
     * @param array $buttons
     * @return InlineKeyboardMarkup
     */
    public function buildInlineKeyboard($buttons = []) : InlineKeyboardMarkup
    {
        if (!empty($this->back))
            $buttons[] = [['text' => '< НАЗАД', 'callback_data' => $this->back]];
        return parent::buildInlineKeyboard($buttons);
    }
}

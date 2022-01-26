<?php

namespace App\Services\Telegram\Invoice;


use App\Models\Record;
use App\Models\TelegramSession;
use App\Models\TelegramUser;
use App\Services\Telegram\TelegramAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class OnlinePayRecord extends TelegramAPI
{
    public function __construct(Request $request, $payload)
    {
        $request_data = $request->all();

        $checkout = isset($request_data["pre_checkout_query"]) ? $request_data["pre_checkout_query"]  : null;
        if(!is_null($checkout)) {
            $this->chat_id = isset($checkout['from']['id']) ? (int)$checkout['from']['id'] : 0;
        }
        parent::__construct($request);

        if($this->chat_id > 0) {

            $payload = $request->input('pre_checkout_query.invoice_payload');
            if(!is_null($payload)) {
                $payload = json_decode($payload, true);
                if (parent::getServiceID() == $payload["service_id"]) {
                    $record = parent::createRecord(false, true);
                    if ($record) {
                        $query = TelegramSession::query()->where("id", $this->getSessionId());
                        $query->update([
                            "record" => $record->id
                        ]);
                        $this->getResponse($request);
                    } else {
                        $this->getResponse($request, false, __('Ошибка 501. Запись не создана.'));
                    }
                }

            }


        } else {
            $this->getResponse($request, false, __('Информация устарела. Пожалуйста, выполните запись заново.'));
        }
    }

    public function getResponse (Request $request, $ok = true, $message = null)
    {
        return $this->bot->answerPreCheckoutQuery($request->input('pre_checkout_query.id'), $ok, $message);
    }

}

<?php

namespace App\Services\Telegram\Invoice;


use App\Services\Telegram\TelegramAPI;
use App\Models\TelegramSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OnlinePayProduct extends TelegramAPI
{
    /*public function __construct(Request $request, $payload)
    {
        parent::__construct($request);
        //$id = (substr($payload, strpos($payload, '_', 1)+1, 100));
        $id = $payload['product_id'];
        $product = \App\Models\Catalog::find($id);
        if ($product && $product->count > 0) {
            $product->writeToReport();
            $this->getResponse($request);
        } else {
            $this->getResponse($request, false, __('Продукт не найден.'));
        }
    }*/

    public function __construct(Request $request, $payload)
    {
        $request_data = $request->all();

        $checkout = isset($request_data["pre_checkout_query"]) ? $request_data["pre_checkout_query"]  : null;
        if(!is_null($checkout)) {
            $this->chat_id = isset($checkout['from']['id']) ? (int)$checkout['from']['id'] : 0;
        }
        parent::__construct($request);

        if($this->chat_id > 0) {

            $id = $payload['product_id'];
            $product = \App\Models\Catalog::find($id);
            if ($product && $product->count > 0) {
                $product->writeToReport();
                $this->getResponse($request);
            } else {
                $this->getResponse($request, false, __('Продукт закончился.'));
            }
        } else {
            $this->getResponse($request, false, __('Информация устарела. Пожалуйста, выполните покупку заново.'));
        }
    }

    public function getResponse (Request $request, $ok = true, $message = null)
    {
        return $this->bot->answerPreCheckoutQuery($request->input('pre_checkout_query.id'), $ok, $message);
    }
}

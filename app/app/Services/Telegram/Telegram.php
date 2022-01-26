<?php

namespace App\Services\Telegram;

use App\Helpers\Beauty\BeautyPro;
use App\Helpers\Yclients\Yclients;
use App\Helpers\Yclients\YclientsException;
use App\Models\Payment;
use App\Models\Record;
use App\Services\Telegram\Invoice\OnlinePayProduct;
use App\Services\Telegram\Invoice\OnlinePayRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\TelegramUser;
use Illuminate\Support\Facades\Log;

class Telegram
{

    private $nsCommands = 'App\\Services\\Telegram\\Commands\\';
    private $nsCallback = 'App\\Services\\Telegram\\Callback\\';

    /**
     * @param Request $request
     * @return bool
     */
    public function doCommand(Request $request): bool
    {

        $dir = dirname(__FILE__).'/Commands/';
        $files = scandir($dir);

        $command = substr($request->input('message.text'), 1);
        $command = ucfirst($command);

        foreach ($files as $file) {

            if ($file == $command.'.php') {
                $class = $this->nsCommands.$command;
                new $class($request);
                return true;
            }
        }

        return false;

    }

    /**
     * @param Request $request
     * @return void
     */
    public function confirmPay(Request $request) {
        $payload = $request->input('pre_checkout_query.invoice_payload');
        if(!is_null($payload)) {
            $payload = json_decode($payload, true);
            $class = $payload["handler"];
            if($class == "OnlinePayRecord") {
                new OnlinePayRecord($request, $payload);
            } elseif($class == "OnlinePayProduct") {
                new OnlinePayProduct($request, $payload);
            }
        }
    }

    /**
     * @param Request $request
     * @throws YclientsException
     */
    public function setSuccessPayment(Request $request) {
        /** @var TelegramUser $telegramUser */
        $telegramUser = $request->input("client");
        if(!is_null($telegramUser)) {
            $record_id = $telegramUser->telegramSession->record;
            if($record_id > 0) {
                $query = Record::query()->where("id", $record_id);
                $query->update(["status" => 1]);
                $query = Payment::query()->where("record_id", $record_id);
                $query->update(["status" => 1]);

                $record = Record::query()->where("id", $record_id)->first();
                if(!is_null($record)) {
                    if (Yclients::isActive()) {
                        // Todo: update record api call
                        //$yclients = new Yclients();
                        //$yclients->api->addRecords([$record]);
                    }

                    // Will upload this record to Beauty Pro CRM
                    if (BeautyPro::isActive()) {
                        $beauty = new BeautyPro();
                        $beauty->api->updateRecords($record->id, "Онлайн оплата: Успешная оплата", "#49cc90");
                    }
                }

            }
        }

    }

    /**
     * @param Request $request
     * @return bool
     */
    public function textCommand(Request $request): bool
    {
        switch ($request->input('message.text')) {
            case 'Запись':
                $class = $this->nsCommands.'TypesOfServices';
                new $class($request);
            break;
            case 'Каталог':
                $class = $this->nsCommands.'Catalog';
                new $class($request);
            break;
            case 'Акции':
                $class = $this->nsCommands.'Shares';
                new $class($request);
            break;
            case 'Отзывы':
                $class = $this->nsCommands.'Review';
                new $class($request);
            break;
            case 'О нас':
                $class = $this->nsCommands.'AboutUs';
                new $class($request);
            break;
            case 'Личный кабинет':
                $class = $this->nsCommands.'Personal';
                new $class($request);
            break;
            default:
                $class = $this->nsCommands.'Text';
                new $class($request);
            break;
        }
        return false;
    }

    /**
     * @param Request $request
     * @return JsonResponse|false
     */
    public function doButton(Request $request) {
        if ($request->input('callback_query.data') == '-')
            return false;
        $data = $request->input('callback_query.data');
        $class = $this->nsCallback.substr($data, 0, strpos($data, '_'));
        $a = new $class($request);
        return response()->json($a->result);
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function getNumber(Request $request): bool
    {
        if ($request->has('client'))
            return $this->redirectToStart($request);

        $client = TelegramUser::create(
            [
                'chat_id'    => $request->input('message.chat.id'),
                'first_name' => $request->input('message.chat.first_name'),
                'last_name'  => $request->input('message.chat.last_name'),
                'username'   => $request->input('message.chat.username'),
                'phone'      => $request->input('message.contact.phone_number'),
            ]
        );

        if ($client) {
            $request->merge(['client' => $client]);
            return $this->redirectToStart($request);
        }
        return false;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function redirectToStart(Request $request): bool
    {
        $class = $this->nsCommands.'Start';
        new $class($request);
        return true;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function rpcAdmin (Request $request)
    {
        $method = $request->input('call');

        if ( !method_exists(RPC::class, $method) )
            return false;

        return RPC::$method($request);
    }
}

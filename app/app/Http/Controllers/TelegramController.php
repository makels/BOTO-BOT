<?php

namespace App\Http\Controllers;

use App\Services\Telegram\Commands\Command;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Facades\TelegramService;
use Illuminate\Support\Facades\URL;

class TelegramController extends Controller
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function main(Request $request)
    {
        $id = null;
        if ($request->has('message.chat.id')) {
            $id = $request->input('message.chat.id');
        }
        else if ($request->has('callback_query.message.chat.id')) {
            $id = $request->input('callback_query.message.chat.id');
        } else {
               Log::info($id . ': Unknown command');
               abort(404);
        }

        if ($request->has('callback_query')) {
            return $this->button($request);
        }
        else if ($request->has('message.entities')) {
            return $this->command($request);
        }
        else if ($request->has('message.contact.phone_number')) {
            return $this->number($request);
        }
        else {
            if ($request->has('message.text')) {
                return $this->text($request);
            }
            return abort(404);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function admin(Request $request)
    {
        if (!$request->has('gess_key') || ($request->input('gess_key') !== env('APP_KEY')))
            return abort('404');
        return TelegramService::rpcAdmin($request);
    }

    /**
     * @param Request $data
     * @return bool
     */
    public function text(Request $data)
    {
        if ($data->has('client'))
            return TelegramService::textCommand($data);
        return TelegramService::redirectToStart($data);
    }

    /**
     * @param Request $data
     * @return bool
     */
    public function command(Request $data)
    {
        if ($data->has('client'))
            return TelegramService::doCommand($data);
        return TelegramService::redirectToStart($data);
    }

    /**
     * @param Request $data
     * @return bool
     */
    public function button(Request $data)
    {
        if ($data->has('client'))
            return TelegramService::doButton($data);
        return TelegramService::redirectToStart($data);
    }

    /**
     * @param Request $data
     * @return bool
     */
    public function number(Request $data)
    {
        return TelegramService::getNumber($data);
    }

    /**
     * @return bool
     * @throws GuzzleException
     */
    public function setWebhook (): bool
    {
        $base_url = URL::to('/');
        try {
            $client = new Client();
            $response = $client->request(
                'POST',
                $base_url.'/api/telegram/admin',
                [
                    'json' => [
                        'gess_key' => getenv('APP_KEY'),
                        'call' => 'setWebhook',
                        'params' => [
                            'url' => $base_url.'/api/telegram'
                        ]
                    ]
                ]
            );

            return $response->getBody()->getContents();

        } catch (\Exception $e) {

            return $e->getMessage();

        }
    }


}

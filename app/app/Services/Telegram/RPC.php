<?php

namespace App\Services\Telegram;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use TelegramBot\Api\Exception;
use TelegramBot\Api\InvalidArgumentException;
use TelegramBot\Api\Types\Message;


class RPC
{
    const BAD_STATUS = 500;
    const OK_STATUS = 200;
    const TELEGRAM_API_URL = 'https://api.telegram.org/bot';

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public static function setWebhook (Request $request): JsonResponse
    {
        $options = ['url' => $request->input('params.url')];
        $base_url = self::TELEGRAM_API_URL . $request->input('token') . '/setWebhook';

        $result_json = file_get_contents($base_url . '?' . http_build_query($options));
        $result_array = json_decode($result_json, JSON_OBJECT_AS_ARRAY);

        if ( !$result_array['ok'] || !$result_array['result'] ) {
            Log::warning($result_json.' *** Webhook cannot set. Bot name = "'.$request->bot_name.'"');
            $status = self::BAD_STATUS;
        }
        else {
            Log::notice($result_json.' *** Webhook was set. Bot name = "'.$request->bot_name.'"');
            $status = self::OK_STATUS;
        }

        return self::getResponse($result_array, $status);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public static function deleteWebhook (Request $request): JsonResponse
    {
        $options = ['drop_pending_updates' => true];
        $base_url = self::TELEGRAM_API_URL . $request->input('token') . '/deleteWebhook';

        $result_json = file_get_contents($base_url . '?' . http_build_query($options));
        $result_array = json_decode($result_json, JSON_OBJECT_AS_ARRAY);

        if ( !$result_array['ok'] || !$result_array['result'] ) {
            Log::error($result_json.' *** Cannot delete webhook. Bot name = "'.$request->bot_name.'"');
            $status = self::BAD_STATUS;
        }
        else {
            Log::notice($result_json.' *** Webhook deleted. Bot name = "'.$request->bot_name.'"');
            $status = self::OK_STATUS;
        }

        return self::getResponse($result_array, $status);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public static function getWebhookInfo (Request $request): JsonResponse
    {
        $options = ['url' => $request->input('params.url')];
        $base_url = self::TELEGRAM_API_URL . $request->input('token') . '/getWebhookInfo';
        $result_json = file_get_contents($base_url . '?' . http_build_query($options));
        return self::getResponse(json_decode($result_json, JSON_OBJECT_AS_ARRAY), self::OK_STATUS);
    }

    /**
     * @param array $data
     * @param int $status
     * @return JsonResponse
     */
    public static function getResponse (array $data, int $status): JsonResponse
    {
        return response()->json($data, $status);
    }

    /**
     * @param Request $request
     * @return Message
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function sendMessage(Request $request): Message
    {
        $bot = new \TelegramBot\Api\BotApi($request->input('token'));
        return $bot->sendMessage($request->input('params.chat_id'), $request->input('params.message'));
    }
}

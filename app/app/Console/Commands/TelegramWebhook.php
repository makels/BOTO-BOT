<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Telegram;

class TelegramWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:webhook';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set Telegram bot webhook';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return string
     */
    public function handle()
    {
        $bot_api_key  = env('TELEGRAM_BOT_TOKEN');
        $bot_username = env('TELEGRAM_USER');
        $hook_url     = env('TELEGRAM_WEBHOOK_URL');

        try {
            // Create Telegram API object
            $telegram = new Telegram($bot_api_key, $bot_username);

            // Set webhook
            $result = $telegram->setWebhook($hook_url);
            if ($result->isOk()) {
                Log::info('Telegram hook set success');
            }
        } catch (TelegramException $e) {
            Log::error($e->getMessage());
        }
    }
}

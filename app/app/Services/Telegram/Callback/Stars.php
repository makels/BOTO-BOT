<?php

namespace App\Services\Telegram\Callback;

use App\Services\MailService;
use Illuminate\Http\Request;

class Stars extends CallbackQuery
{
    public $business;

    public function __construct(Request $request)
    {
        $this->business = $request->input("slug", "");
        parent::__construct($request);
        if (isset($this->getData()->Review)) {
            return $this->review();
        }
        return false;
    }

    private function review ()
    {
        $user_id = $this->user->id;
        $stars = parent::setStarts();
        $text = parent::getData()->Review;

        $review = \App\Models\Review::create(
            [
                'telegram_user_id' => $user_id,
                'stars' => $stars,
                'text' => $text
            ]
        );

        if ($review) {
            parent::resetData();
            parent::editMessage('Спасибо за Ваш отзыв!');
            MailService::sendReviewCreate($this->business, $user_id, $stars, $text);
        }
    }
}

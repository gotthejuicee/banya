<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendOrderToTelegram implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order $order,
    ) {}

    public function handle(): void
    {
        $token = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (blank($token) || blank($chatId)) {
            Log::info('Telegram не налаштовано — заявка лишилась тільки в базі.', [
                'order_id' => $this->order->id,
            ]);

            return;
        }

        $product = e($this->order->product?->name ?? 'Не вказано');
        $comment = filled($this->order->comment)
            ? e($this->order->comment)
            : '—';

        $text = implode("\n", [
            '🔥 <b>Нова заявка з IDI_V_BANYU__</b>',
            '',
            "🎁 Набір: <b>{$product}</b>",
            '👤 Ім’я: '.e($this->order->name),
            "📞 Телефон: <code>{$this->order->phone}</code>",
            "💬 Коментар: {$comment}",
            '',
            '🕒 '.$this->order->created_at->timezone('Europe/Kyiv')->format('d.m.Y H:i'),
        ]);

        try {
            Http::asForm()
                ->timeout(10)
                ->post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $text,
                    'parse_mode' => 'HTML',
                ])
                ->throw();
        } catch (Throwable $e) {
            // Токен бота є частиною URL — у лог він потрапити не повинен
            Log::warning('Не вдалося надіслати заявку в Telegram: '.str_replace($token, '[REDACTED]', $e->getMessage()), [
                'order_id' => $this->order->id,
            ]);
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Visit;
use Illuminate\Console\Command;

class ClearVisits extends Command
{
    protected $signature = 'visits:clear {--force : Видалити без підтвердження}';

    protected $description = 'Очистити статистику відвідувачів (таблиця visits). Заявки не чіпає.';

    public function handle(): int
    {
        $count = Visit::query()->count();

        if ($count === 0) {
            $this->info('Статистика вже порожня — нема чого видаляти.');

            return self::SUCCESS;
        }

        // Дані незворотні: бекапу visits немає, тож без --force питаємо.
        if (! $this->option('force')
            && ! $this->confirm("Видалити {$count} записів статистики? Заявки лишаться на місці.")) {
            $this->comment('Скасовано.');

            return self::FAILURE;
        }

        Visit::query()->truncate();

        $this->info("Видалено записів: {$count}. Лічильники в адмінці — з нуля.");

        return self::SUCCESS;
    }
}

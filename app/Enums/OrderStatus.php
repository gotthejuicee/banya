<?php

namespace App\Enums;

enum OrderStatus: string
{
    case New = 'new';
    case InProgress = 'in_progress';
    case Done = 'done';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::New => 'Нова',
            self::InProgress => 'В роботі',
            self::Done => 'Виконана',
            self::Rejected => 'Відхилена',
        };
    }
}

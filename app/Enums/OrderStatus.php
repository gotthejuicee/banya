<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum OrderStatus: string implements HasColor, HasLabel
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

    public function getLabel(): string
    {
        return $this->label();
    }

    public function getColor(): string
    {
        return match ($this) {
            self::New => 'warning',
            self::InProgress => 'info',
            self::Done => 'success',
            self::Rejected => 'danger',
        };
    }
}

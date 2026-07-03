<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Product;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    /**
     * Слаг генерується з назви автоматично, щоб адміну не думати про нього.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $base = Str::slug($data['name']) ?: 'nabir';
        $slug = $base;
        $i = 2;

        while (Product::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        $data['slug'] = $slug;

        return $data;
    }
}

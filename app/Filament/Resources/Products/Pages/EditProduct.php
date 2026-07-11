<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Product;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * FileUpload на диску storage не бачить bundled `images/products/gallery/*`.
     * Порожній state у формі не повинен зносити карусель (стрілки).
     * Якщо галерею вже змило — відновлюємо дефолт по slug.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['badge']);

        $incoming = $data['gallery'] ?? null;
        $incomingEmpty = $incoming === null
            || $incoming === []
            || $incoming === '';

        if (! $incomingEmpty) {
            return $data;
        }

        $existing = $this->record->gallery ?? [];
        $hasBundled = is_array($existing) && collect($existing)->contains(
            fn ($path) => is_string($path) && str_starts_with($path, 'images/'),
        );

        if ($hasBundled) {
            $data['gallery'] = $existing;
        } else {
            $fallback = Product::defaultGalleryForSlug((string) $this->record->slug);
            if ($fallback !== null) {
                $data['gallery'] = $fallback;
            } else {
                unset($data['gallery']);
            }
        }

        return $data;
    }
}

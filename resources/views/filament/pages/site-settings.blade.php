<x-filament-panels::page>
    <form wire:submit="save" class="grid gap-y-6">
        {{ $this->form }}

        <div>
            <x-filament::button type="submit">
                Зберегти
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>

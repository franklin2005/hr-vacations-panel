<x-filament-panels::page>
    {{ $this->form }}

    <x-filament::button class="mt-4" wire:click="submit">
        Submit request
    </x-filament::button>
</x-filament-panels::page>

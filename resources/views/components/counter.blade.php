<?php

use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    // component state
    public int $count = 0;
    public int $incrementValue = 1;

    // lifecycle hook executed once to initialize state
    public function mount(int $count = 0, int $increment = 1): void
    {
        $this->count = $count;
        $this->incrementValue = $increment;
    }

    // increment the counter by the specified increment value
    public function increment(): void
    {
        $this->count += $this->incrementValue;
    }

    // decrement the counter by the specified increment value
    public function decrement(): void
    {
        $this->count -= $this->incrementValue;
    }

    // reset the counter state to initial values
    public function resetState(): void
    {
        $this->count = 0;
        $this->incrementValue = 1;
    }

    // compute the status based on the current count
    #[Computed]
    public function status(): string
    {
        return match (true) {
            $this->count > 0 => 'Positive',
            $this->count < 0 => 'Negative',
            default           => 'Neutral',
        };
    }
};
?>

<div class="card w-96 mt-10 mx-auto">

    <div class="flex items-center justify-center gap-2"> 
        <x-form.label for="incrementValue">Increment</x-form.label>     
        <x-form.range min="1" max="10" name="incrementValue" type="number" label="Increment Value" value="{{ $incrementValue }}" wire:model="incrementValue" />
        <button wire:click="resetState" class="btn btn-light">Reset</button>
    </div>
    <div class="flex items-center justify-center mt-5">
        <button wire:click="decrement" class="btn btn-light">-</button>
        <div class="flex items-center mx-4">
            <span class="mx-4 text-lg font-medium">{{ $count }}</span>
            <span class="badge badge-yellow"> {{ $this->status }}</span>
        </div>
        <button wire:click="increment" class="btn btn-light">+</button>
    </div>

</div>
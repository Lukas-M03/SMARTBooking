@props([
    'name',
    'mode',
    'label' => null,
    'labelClass' => '',
    'hint' => null,
    'hintId' => null,
    'hintClass' => 'text-sm text-gray-500 mb-3',
    'value' => null,
    'type' => 'text',
    'options' => []
])

@if ($label)
    <x-form.label for="{{ $name }}" class="{{ $labelClass }}">{{ $label }}</x-form.label>
@endif

@if ($hint)
    <p @if($hintId) id="{{ $hintId }}" @endif class="{{ $hintClass }}">{{ $hint }}</p>
@endif

@if($mode === 'input')
    <x-form.input {{ $attributes->merge(['id' => $name, 'name' => $name, 'value' => $value, 'type' => $type]) }} />
@elseif($mode === 'textarea')
    <x-form.textarea {{ $attributes->merge(['id' => $name, 'name' => $name, 'value' => $value]) }} />
@elseif($mode === 'checkbox')
    <x-form.toggle {{ $attributes->merge(['id' => $name, 'name' => $name, 'value' => $value]) }} />  
@elseif($mode === 'select')
    <x-form.select {{ $attributes->merge(['id' => $name, 'name' => $name, 'value' => $value]) }} :options="$options">{{ $slot }}</x-form.select>
@elseif($mode === 'range')
    <x-form.range {{ $attributes->merge(['id' => $name, 'name' => $name, 'value' => $value]) }} />
@else
    @php 
        throw new \Exception("The x-form.group mode value of {$mode} is invalid");    
    @endphp
@endif

<x-form.error name="{{ $name }}"/>


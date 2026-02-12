@props([
    'name',
    'mode',
    'label' => null,
    'value' => null,
    'type' => 'text',
    'options' => []
])

@if ($label)
    <x-form.label for="{{ $name }}">{{ $label }}</x-form.label>
@endif

@if($mode === 'input')
    <x-form.input {{ $attributes->merge(['id' => $name, 'name' => $name, 'value' => $value, 'type' => $type]) }} />
@elseif($mode === 'textarea')
    <x-form.textarea {{ $attributes->merge(['id' => $name, 'name' => $name, 'value' => $value]) }} />
@elseif($mode === 'checkbox')
    <x-form.toggle {{ $attributes->merge(['id' => $name, 'name' => $name, 'value' => $value]) }} />  
@elseif($mode === 'select')
    <x-form.select {{ $attributes->merge(['id' => $name, 'name' => $name, 'value' => $value]) }} :options="$options" />
@elseif($mode === 'range')
    <x-form.range {{ $attributes->merge(['id' => $name, 'name' => $name, 'value' => $value]) }} />
@else
    @php 
        throw new \Exception("The x-form.group mode value of {$mode} is invalid");    
    @endphp
@endif

<x-form.error name="{{ $name }}"/>


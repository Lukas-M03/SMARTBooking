@props([
    'name',
    'value' => null,
    'options' => [],
    'placeholder' => 'Choose option...'
])

<select {{ $attributes->merge(['id' => $name, 'name' => $name])->class('bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5') }}>
    @if($placeholder && empty($options)) 
        <option value="" @selected(empty($value))>{{ $placeholder }}</option>
    @endif
    @if(!empty($options))
        @foreach ($options as $key => $label)
            <option value="{{ $key }}" @selected((string)$value == (string)$key)>
                {{ $label }}
            </option>
        @endforeach
    @else
        {{ $slot }}
    @endif
</select>
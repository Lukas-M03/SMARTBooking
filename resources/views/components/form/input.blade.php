@props([
    'name',
    'value' => '',
    'type' => 'text',
])

<input {{ $attributes->merge([
            'id' => $name, 
            'name' => $name, 
            'value' => $value, 
            'type' => $type
        ])->class([
            'bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5',
            'file:mr-2 file:py-1 file:px-3 file:rounded-l-md file:border-0 file:text-sm file:font-bold hover:file:cursor-pointer hover:file:opacity-80 file:bg-gray-900 file:text-white' => $type === 'file' 
        ])
}} />

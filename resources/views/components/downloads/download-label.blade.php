<label @class([
    "inline-flex items-center px-3 py-0.5 text-sm leading-5 font-medium transition duration-150 ease-in-out border rounded-lg shadow-sm",
    'bg-green-100 text-green-700 border-green-300' => $type == 'success',
    'bg-red-100 text-red-700 border-red-300' => $type == 'error',
    'bg-yellow-100 text-yellow-700 border-yellow-300' => $type == 'warning',
    'bg-blue-100 text-blue-700 border-blue-300' => $type == 'info',
    'bg-white text-gray-700 border-gray-300' => $type == 'default',
    $attributes->has('class') ? $attributes->get('class') : null
])>
    {{ $slot }}
</label>

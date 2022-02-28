
    <div class="flex flex-row justify-between items-center">
        <div class="pl-2">
            <h2 class="text-lg leading-6 font-medium text-gray-900 max-w-3xl truncate">
                {{ $download->title ? $download->title : "No title yet" }}
            </h2>
            <a class="text-sm leading-5 rounded-lg text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900" href="{{ $download->url }}">
                {{ $download->url }}
            </a>
        </div>
        <div>
            {{-- Show download->status --}}
            @php
                $status_map = [
                    'pending' => 'warning',
                    'downloading' => 'info',
                    'success' => 'success',
                    'error' => 'error',
                ];

                $status = $status_map[$download->status];
            @endphp
            <x-downloads.download-label :type="$status" >
                {{ $download->status }}
            </x-downloads.download-label>
            <x-downloads.download-label type="default" >
                {{ $download->created_at->diffForHumans() }}
            </x-downloads.download-label>
            @if ($download->status == 'success')
                <x-downloads.download-files-label :download="$download" />
            @endif
            <x-downloads.delete-label :id="$download->id" />
        </div>
    </div>
<hr class="mt-6 border-gray-200">

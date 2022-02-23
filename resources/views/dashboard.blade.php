<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form method="POST" action="{{ route('downloads.store') }}">
                    @csrf
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Create a new download
                            </h3>
                        </div>
                    </div>
                    <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                    <input type="url" name="url" class="mt-6 form-input block w-full py-3 px-4 placeholder-gray-500 rounded-md shadow-sm focus:outline-none focus:shadow-outline-blue focus:border-blue-300 transition duration-150 ease-in-out sm:text-sm sm:leading-5" placeholder="URL">
                    <button type="submit" class="mt-6">
                        Download
                    </button>
                </form>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-12">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Your downloads ({{ $downloads->count() }})
                </h3>
                <div class="mt-6">
                    @foreach ($downloads as $download)
                        <form method="POST" action="{{ route('downloads.destroy', $download->id) }}">
                            @csrf
                            @method('DELETE')
                            <div class="flex flex-row justify-between items-center">
                                <a class="px-2 py-1 text-sm leading-5 rounded-lg text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900" href="{{ $download->url }}">
                                    {{ $download->url }}
                                </a>
                                <div>
                                    {{-- Show download->status --}}
                                    <label @class([
                                        'inline-flex items-center px-3 py-0.5 mt-2 text-sm leading-5 font-medium transition duration-150 ease-in-out bg-white border rounded-lg shadow-sm',
                                        'bg-green-100 text-green-700 border-green-300' => $download->status == 'success',
                                        'bg-sky-100 text-sky-700 border-sky-300' => $download->status == 'downloading',
                                        'bg-red-100 text-red-700 border-red-300' => $download->status == 'error',
                                        'bg-yellow-100 text-yellow-700 border-yellow-300' => $download->status == 'pending',
                                        ])>
                                        {{ $download->status }}
                                    </label>
                                    <label class="inline-flex items-center px-3 py-0.5 mt-2 text-sm leading-5 font-medium text-gray-700 transition duration-150 ease-in-out bg-white border border-gray-300 rounded-lg shadow-sm">
                                        {{ $download->created_at->diffForHumans() }}
                                    </label>
                                    <button action="submit" class="inline-flex items-center px-3 py-0.5 mt-2 text-sm leading-5 font-medium text-red-700 transition duration-150 ease-in-out bg-white border border-red-300 rounded-lg shadow-sm hover:text-white hover:bg-red-300 focus:outline-none focus:shadow-outline-red focus:border-red-300">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </form>
                        <hr class="mt-6 border-gray-200">
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

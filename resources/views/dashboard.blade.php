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
                        <x-downloads.download-entry :download="$download" />
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

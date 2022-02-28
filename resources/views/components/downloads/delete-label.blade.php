<form method="POST" action="{{ route('downloads.destroy', $id) }}" class="inline">
    @csrf
    @method('DELETE')
    <button action="submit" class="items-center px-3 py-0.5 text-sm leading-5 font-medium text-red-700 transition duration-150 ease-in-out bg-white border border-red-300 rounded-lg shadow-sm hover:text-white hover:bg-red-300 focus:outline-none focus:shadow-outline-red focus:border-red-300">
        Delete
    </button>
</form>

<x-downloads.download-label type="success" >
    <button type="button" onclick="
        let files = {{ Illuminate\Support\Js::from($download->files) }};
        files = files.map(file => file.path.replace('./', '/').replace('/app/public', ''));
        files.forEach(file => {
            window.open(file, '_blank');
        });">
        Download
    </button>
</x-downloads.download-label>

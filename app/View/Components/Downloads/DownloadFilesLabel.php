<?php

namespace App\View\Components\Downloads;

use Illuminate\View\Component;
use App\Models\Download;

class DownloadFilesLabel extends Component
{
    /**
     * Download
     *
     * @var Download
    */
    public $download;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(Download $download)
    {
        $this->download = $download;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.downloads.download-files-label');
    }
}

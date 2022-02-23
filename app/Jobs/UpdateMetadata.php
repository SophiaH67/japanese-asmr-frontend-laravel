<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Download;
use Illuminate\Support\Facades\Log;

class UpdateMetadata implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The download instance.
     *
     * @var Download
     */
    protected $download;

    /**
     * Create a new job instance.
     *
     * @param  Download  $download
     * @return void
     */
    public function __construct(Download $download)
    {
        $this->download = $download->withoutRelations();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $download = Download::findOrFail($this->download->id);

        $html = file_get_contents($download->url);
        // Extract the title from the HTML.
        preg_match('/<title>(.*)<\/title>/', $html, $matches);
        $title = $matches[1];

        // All titles end with " – Japanese ASMR", so remove that.
        $title = str_replace(' &#8211; Japanese ASMR', '', $title);

        $download->update([
            'title' => $title,
        ]);
    }
}

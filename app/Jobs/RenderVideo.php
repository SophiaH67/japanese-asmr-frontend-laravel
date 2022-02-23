<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Process;
use App\Models\Download;

class RenderVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The download instance.
     *
     * @var Download
     */
    protected $download;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 1500;

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
        $this->download = Download::findOrFail($this->download->id);
        $this->download->update([
            'status' => 'downloading',
        ]);

        $command = ['japanese-asmr', $this->download->url];

        if ($this->download->output_path !== null) {
            $command[] = $this->download->output_path;
        }

        $process = new Process($command);

        $process->setTimeout($this->timeout);
        $process->setIdleTimeout($this->timeout);

        $process->run();

        $this->download->update([
            'status' => $process->isSuccessful() ? 'success' : 'error',
        ]);
    }
}

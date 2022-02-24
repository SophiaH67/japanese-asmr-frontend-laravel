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
use Illuminate\Support\Facades\Log;

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

        $output_folder = "./storage/app/public/{$this->download->title}/";
        // Recursively create the output folder if it doesn't exist.
        if (!is_dir($output_folder)) {
            mkdir($output_folder, 0777, true);
        }
        $command = ['japanese-asmr', $this->download->url, $output_folder];

        $process = new Process($command);

        $process->setTimeout($this->timeout);
        $process->setIdleTimeout($this->timeout);

        // If there are .mp4 files in output folder, assume the job is already done.
        if (glob("{$output_folder}*.mp4") == []) {
            $process->run();

            if (!$process->isSuccessful()) {
                Log::error("Command failed: {$process->getCommandLine()} with output: {$process->getErrorOutput()}");
                $this->download->update([
                    'status' => 'error',
                ]);
                return;
            }
        }

        $this->download->update([
            'status' => 'success',
        ]);

        foreach (glob("{$output_folder}*.mp4") as $file) {
            $this->download->files()->create([
                'path' => $file,
                'download_id' => $this->download->id,
            ]);
        }
    }
}

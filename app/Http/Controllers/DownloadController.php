<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDownloadRequest;
use App\Http\Requests\UpdateDownloadRequest;
use App\Models\Download;
use App\Models\File;
use App\Jobs\UpdateMetadata;
use App\Jobs\RenderVideo;
use Illuminate\Support\Facades\Bus;

class DownloadController extends Controller
{
    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Download::class, 'download');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $downloads = $user->downloads()->orderBy('created_at', 'desc')->paginate(10);
        return view('dashboard', compact('downloads'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // This method is not used.
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreDownloadRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDownloadRequest $request)
    {
        $user = auth()->user();
        $download = $user->downloads()->create($request->validated());
        $download->save();
        Bus::chain([
            new UpdateMetadata($download),
            new RenderVideo($download),
        ])->dispatch();
        return redirect()->route('dashboard');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Download  $download
     * @return \Illuminate\Http\Response
     */
    public function show(Download $download)
    {
        // This method is not used.
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Download  $download
     * @return \Illuminate\Http\Response
     */
    public function edit(Download $download)
    {
        // This method is not used.
        return redirect()->route('dashboard');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateDownloadRequest  $request
     * @param  \App\Models\Download  $download
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDownloadRequest $request, Download $download)
    {
        // This is always unauthorized.
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Download  $download
     * @return \Illuminate\Http\Response
     */
    public function destroy(Download $download)
    {
        foreach ($download->files as $file) {
            // If this instance is the only one in the database, delete the file.
            $files_referencing_this_path = File::where('path', $file->path)->count();
            if ($files_referencing_this_path == 1) {
                $actual_path = "../{$file->path}";
                if (file_exists($actual_path)) {
                    Log::emergency("File {$file->path} exists.");
                    unlink($actual_path);
                }
            }
            $file->delete();
        }
        $download->delete();
        return redirect()->route('dashboard');
    }
}

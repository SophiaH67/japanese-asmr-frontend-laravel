<?php

namespace App\Models;

use App\Models\Download;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'download_id',
        'path',
    ];

    public function download()
    {
        return $this->belongsTo(Download::class);
    }
}

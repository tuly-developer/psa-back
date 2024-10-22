<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UploadXlsFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private string $filePath, private string $originalName, private int $fileUploadId, private ?int $userId) {}

    public function handle()
    {
        sleep(1);
        $file = file_get_contents($this->filePath);

        $response = Http::attach(
            'file',
            $file,
            basename($this->filePath)
        )->post(env('FILE_CONVERTER_URL') . '/v1/modelos/xlsx', [
            'userId' => $this->userId,
            'fileUploadId' => $this->fileUploadId,
            'fileName' => $this->originalName
        ]);

        if ($response->failed()) Log::error('Failed to upload the file.', ['error' => $response->body()]);

        Storage::delete($this->filePath);
    }
}

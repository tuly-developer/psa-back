<?php

namespace App\Jobs;

use PharData;
use App\Models\FileUpload;
use App\Enums\FileStatusEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessTargzFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private string $filePath, private string $originalName, private string $fileUploadId, private ?int $userId, private string $uniqueName) {}

    public function handle()
    {
        $extractPath = storage_path('app/extracted/' . $this->originalName);
        $htmPath = storage_path('app/public/targz/htm/' . $this->originalName);
        $xlsPath = storage_path('app/public/targz/xls/' . $this->originalName);

        if (!file_exists($extractPath)) mkdir($extractPath, 0755, true);
        if (!file_exists($htmPath)) mkdir($htmPath, 0755, true);
        if (!file_exists($xlsPath)) mkdir($xlsPath, 0755, true);

        try {
            $tarGzFile = $this->filePath . '/' . $this->uniqueName . '.tar.gz';
            $tarFile = $this->filePath . '/' . $this->uniqueName . '.tar';

            // Decomprime el archivo .tar.gz a .tar
            $phar = new PharData($tarGzFile);
            $phar->decompress(); // Esto elimina la extensión .gz y crea el archivo .tar

            // Extrae el archivo .tar al directorio de extracción
            $phar = new PharData($tarFile);
            $phar->extractTo($extractPath);

            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($extractPath));

            foreach ($files as $file) {
                if ($file->isFile() && $file->getExtension() === 'xls') {
                    File::move($file->getRealPath(), $xlsPath . '/' . $file->getFilename());
                    UploadXlsFileJob::dispatch($xlsPath . '/' . $file->getFilename(), $this->originalName, $this->fileUploadId, $this->userId);
                }

                if ($file->isFile() && $file->getExtension() === 'htm') {
                    File::move($file->getRealPath(), $htmPath . '/' . $file->getFilename());
                    ProcessHtmFileJob::dispatch($htmPath . '/' . $file->getFilename(), $this->originalName, $this->fileUploadId, $this->userId);
                }
            }

            Storage::delete($this->filePath);
            Storage::delete($extractPath);
            FileUpload::find($this->fileUploadId)->update(['status' => FileStatusEnum::COMPLETED]);
        } catch (\Exception $e) {
            Storage::delete($this->filePath);
            Storage::delete($extractPath);

            FileUpload::where('id', $this->fileUploadId)->update([
                // 'filename' => DB::raw("CONCAT(filename, ' - error')"),
                'status' => FileStatusEnum::ERROR,
                'error' => $e->getMessage(),
            ]);
            Log::error($e->getMessage(), ['filePath' => $this->filePath]);
        }
    }
}

<?php

namespace App\Jobs;

use App\Models\Rendicion;
use App\Models\FileUpload;
use App\Enums\FileStatusEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessXlsFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private string $filePath, private string $fileName, private string $fileUploadId, private ?int $userId) {}

    public function handle(): void
    {

        try {

            $file = Storage::path($this->filePath);
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();
            $headers = array_shift($data);

            $batchData = [];
            $batchSize = Rendicion::BATCH_SIZE;

            foreach ($data as $row) {
                $rowData = array_combine($headers, $row);

                // if (!$rowData['DOMINIO']) continue;
                if (!array_key_exists('DOMINIO', $rowData)) continue;

                $batchData[] = [
                    'file_upload_id' => $this->fileUploadId,
                    'archivo' => $this->fileName,
                    'id_tramite' => $rowData['ID'] ?? null, //! SE PONE EL 'ID_' AL PRINCIPIO PORQUE NO ES UNA RELACIÓN (TODAVIA)
                    'fecha_tramite' => $rowData['FECHA'] ?? null,
                    'estado_tramite' => $rowData['ESTADO'] ?? null,
                    'dominio' => $rowData['DOMINIO'] ?? null,
                    'acta' => $rowData['ACTA'] ?? null,
                    'codigo' => $rowData['CODIGO'] ?? null,
                    'importe' => $rowData['IMPORTE'] ?? null,
                    'id_comprobante' => $rowData['ID COMPROBANTE'] ?? null, //! SE PONE EL 'ID_' AL PRINCIPIO PORQUE NO ES UNA RELACIÓN (TODAVIA)
                    'user_id' => $this->userId,
                ];

                if (count($batchData) >= $batchSize) {
                    Rendicion::insert($batchData);
                    $batchData = [];
                }
            }

            if (!empty($batchData)) Rendicion::insert($batchData);

            FileUpload::find($this->fileUploadId)->update(['status' => FileStatusEnum::COMPLETED]);
            Storage::delete($this->filePath);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['filePath' => $this->filePath]);
        }
    }
}

<?php

namespace App\Jobs;

use App\Models\Cobranza;
use App\Models\FileUpload;
use App\Enums\FileStatusEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessPaymentFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private string $filePath, private string $fileUploadId, private ?int $userId) {}

    public function handle(): void
    {

        try {

            FileUpload::find($this->fileUploadId)->update(['status' => FileStatusEnum::PROCESSING]);

            $file = Storage::path($this->filePath);
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();
            $headers = array_shift($data);

            $expectedHeaders = [
                'SISTEMA',
                'TRAMITE',
                'ESTADO TRAMITE',
                'FECHA TRAMITE',
                'IMPORTE',
                'RENDICION',
                'TIPO',
                'ENTIDAD',
                'ESTADO PAGO',
                'MONTO PAGADO',
                'FECHA PAGO',
            ];

            if ($headers !== $expectedHeaders) {
                Log::error('Encabezados de archivo Excel no coinciden con los esperados', ['filePath' => $this->filePath]);
                throw new \Exception('Encabezados incorrectos');
            }

            $batchData = [];
            $batchSize = Cobranza::BATCH_SIZE;

            foreach ($data as $row) {
                $rowData = array_combine($headers, $row);

                if (!$rowData['FECHA TRAMITE']) continue;

                $fechaTramite = $rowData['FECHA TRAMITE'] ?? null;
                if (is_numeric($fechaTramite)) {
                    $dateObject = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fechaTramite);
                    $fechaTramite = $dateObject ? $dateObject->format('Y-m-d') : null;
                }

                $fechaPago = $rowData['FECHA PAGO'] ?? null;
                if (is_numeric($fechaPago)) {
                    $dateObject = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fechaPago);
                    $fechaPago = $dateObject ? $dateObject->format('Y-m-d') : null;
                }

                $batchData[] = [
                    'file_upload_id' => $this->fileUploadId,
                    'sistema_origen' => $rowData['SISTEMA'] ?? null,
                    'id_tramite' => $rowData['TRAMITE'] ?? null, //! SE PONE EL 'ID_' AL PRINCIPIO PORQUE NO ES UNA RELACIÓN (TODAVIA)
                    'estado_tramite' => $rowData['ESTADO TRAMITE'] ?? null,
                    'fecha_tramite' => $fechaTramite,
                    'importe' => $rowData['IMPORTE'] ?? null,
                    'rendicion' => $rowData['RENDICION'] ?? null,
                    'tipo' => $rowData['TIPO'] ?? null,
                    'entidad_bancaria' => $rowData['ENTIDAD'] ?? null,
                    'estado_pago' => $rowData['ESTADO PAGO'] ?? null,
                    'monto_pagado' => $rowData['MONTO PAGADO'] ?? null,
                    'fecha_pago' => $fechaPago,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'user_id' => $this->userId,
                ];

                if (count($batchData) >= $batchSize) {
                    Cobranza::insert($batchData);
                    $batchData = [];
                }
            }

            if (!empty($batchData)) Cobranza::insert($batchData);

            FileUpload::find($this->fileUploadId)->update(['status' => FileStatusEnum::COMPLETED]);
            Storage::delete($this->filePath);
        } catch (\Exception $e) {
            FileUpload::where('id', $this->fileUploadId)->update([
                // 'filename' => DB::raw("CONCAT(filename, ' - error')"),
                'status' => FileStatusEnum::ERROR,
                'error' => $e->getMessage()
            ]);
            Log::error($e->getMessage(), ['filePath' => $this->filePath]);
        }
    }
}

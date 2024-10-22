<?php

namespace App\Jobs;

use App\Models\Safit;
use App\Models\FileUpload;
use App\Enums\FileStatusEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessSafitFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private string $filePath, private string $fileUploadId, private ?int $userId) {}

    public function handle(): void
    {
        try {

            FileUpload::find($this->fileUploadId)->update(['status' => FileStatusEnum::PROCESSING]);

            $html = Storage::get($this->filePath);
            $dom = new \DOMDocument();
            @$dom->loadHTML($html);
            $xpath = new \DOMXPath($dom);

            $rows = $xpath->query("//tr[@class='BorderResultado']");

            foreach ($rows as $row) {
                $columns = $row->getElementsByTagName('td');

                $n_rendicion = trim($columns->item(0)->textContent);
                $tipo = trim($columns->item(1)->textContent);
                $estado = trim($columns->item(2)->textContent);
                $entidad = trim($columns->item(3)->textContent);
                $cuenta = trim($columns->item(4)->textContent);
                $cantidad = trim($columns->item(5)->textContent);
                $total = trim(str_replace(",", "", $columns->item(6)->textContent));
                $sist_reg = trim(str_replace(",", "", $columns->item(7)->textContent));
                $safit = trim(str_replace(",", "", $columns->item(8)->textContent));
                $cenat = trim(str_replace(",", "", $columns->item(9)->textContent));
                $ci = trim(str_replace(",", "", $columns->item(10)->textContent));
                $pv = trim(str_replace(",", "", $columns->item(11)->textContent));
                $trgs = trim(str_replace(",", "", $columns->item(12)->textContent));
                $trgs_pv = trim(str_replace(",", "", $columns->item(13)->textContent));
                $sirto = trim(str_replace(",", "", $columns->item(14)->textContent));
                $renatedu = trim(str_replace(",", "", $columns->item(15)->textContent));
                $fecha_ren = trim($columns->item(16)->textContent);
                $fecha_reg = trim($columns->item(17)->textContent);

                Safit::create(
                    [
                        'n_rendicion' => $n_rendicion,
                        // ],
                        // [
                        'file_upload_id' => $this->fileUploadId,
                        'tipo' => $tipo,
                        'estado' => $estado,
                        'entidad_bancaria' => $entidad,
                        'cuenta' => $cuenta,
                        'cantidad' => $cantidad,
                        'total' => $total,
                        'sist_reg' => $sist_reg,
                        'safit' => $safit,
                        'cenat' => $cenat,
                        'ci' => $ci,
                        'pv' => $pv,
                        'trgs' => $trgs,
                        'trgs_pv' => $trgs_pv,
                        'sirto' => $sirto,
                        'renatedu' => $renatedu,
                        'fecha_rendicion' => $fecha_ren,
                        'fecha_registro' => $fecha_reg,
                        'user_id' => $this->userId
                    ]
                );
            }

            FileUpload::find($this->fileUploadId)->update(['status' => FileStatusEnum::COMPLETED]);

            //TODO Preguntar si se debe eliminar el archivo, si se elimina borrar el campo 'url' de la tabla 'file_uploads'
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

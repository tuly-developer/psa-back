<?php

namespace App\Jobs;

use App\Models\Htm;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;

class ProcessHtmFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private string $filePath, private string $originalName, private int $fileUploadId, private ?int $userId) {}

    public function handle()
    {
        $file = new \SplFileInfo($this->filePath);

        $htmlContent = file_get_contents($file->getRealPath());

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($htmlContent);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);

        $getNodeValue = function ($query) use ($xpath) {
            $result = $xpath->query($query);
            return $result->length > 0 ? trim($result->item(0)->nodeValue) : null;
        };

        // Nombre archivo
        $filename = $this->originalName ? basename($this->filePath) : basename($this->filePath);
        $fileNameParts = explode('_', pathinfo($filename, PATHINFO_FILENAME));

        // Buscamos si es Municipio o Provincia
        $municipalidadProvinciaNode = $getNodeValue("//b[contains(text(), 'Sres.')]") ?? '';
        $municipalidadProvinciaParts = explode(' ', $municipalidadProvinciaNode);
        $municipalidadProvincia = $municipalidadProvinciaParts[1] == "Municipalidad" ? "Municipalidad" : "Provincia";

        // Busco datos 'periodo'
        $periodoNode = $getNodeValue("//b[contains(text(), 'odo de Rendici')]//following-sibling::text()[1]");
        $periodoParts = explode('/', $periodoNode);
        $periodoInicio = trim($periodoParts[0] ?? '');
        $periodoFin = trim($periodoParts[1] ?? '');

        // Busco dato 'numero de rendición'
        $numeroRendicionNode = $getNodeValue("//b[contains(text(), 'de Rendici')]//following-sibling::text()[1]");
        $numeroRendicion = trim(str_replace(':', '', $numeroRendicionNode));

        // Busco dato 'fecha y hora del proceso'
        $fechaHoraProcesoNode = $getNodeValue("//b[contains(text(), 'Fecha de Proceso')]//following-sibling::text()[1]");
        $fechaHoraProceso = trim(str_replace(': ', '', $fechaHoraProcesoNode));

        // Busco datos 'bancarios'
        $nombreBanco = $getNodeValue("//b[contains(text(), 'Depositado en Banco')]//following-sibling::text()[1]");

        $numeroCuentaBancariaNode = $getNodeValue("//b[contains(text(), 'Cuenta Bancaria N')]//following-sibling::text()[1]");
        $numeroCuentaBancaria = trim(str_replace(' (', '', $numeroCuentaBancariaNode));

        $detalleCuentaBancariaNode = $getNodeValue("//b[contains(text(), 'a la orden de')]//following-sibling::text()[1]");
        $detalleCuentaBancaria = trim(str_replace([': ', ')'], '', $detalleCuentaBancariaNode));

        // CBU - CUIT
        $cbu = $getNodeValue("//b[contains(text(), 'C.B.U')]//following-sibling::text()[1]");
        $cuit = $getNodeValue("//b[contains(text(), 'C.U.I.T')]//following-sibling::text()[1]");

        $totalInfracciones = $getNodeValue("//td[contains(text(), 'Total de Infracciones')]//following-sibling::td[1]");
        $totalInfracciones = str_replace(['$', '.', ' ', ','], ['', '', '', '.'], $totalInfracciones);

        $gastosBancariosRecaudadora = $getNodeValue("//td[contains(text(), 'Gastos Bancarios')]//following-sibling::td[1]");
        $gastosBancariosRecaudadora = str_replace(['$', '.', ' ', ','], ['', '', '', '.'], $gastosBancariosRecaudadora);

        $subtotal = $getNodeValue("//td[b[text()='Subtotal']]//following-sibling::td[1]");
        $subtotal = str_replace(['$', '.', ' ', ','], ['', '', '', '.'], $subtotal);

        // Busco datos para el array 'lugar'
        $lugarNode = $getNodeValue("//td[contains(text(), '" . $municipalidadProvincia . " de')]");
        list($l_nombre, $l_porcentaje) = explode("(", $lugarNode);
        $l_nombre = trim($l_nombre);

        $l_porcentaje = trim(rtrim($l_porcentaje, "%)"));
        $l_porcentaje = rtrim(number_format((float)$l_porcentaje, 2, '.', ''), '.') ?: '0';

        $l_total = $getNodeValue("//td[contains(text(), '" . $municipalidadProvincia . " de')]//following-sibling::td[1]");
        $l_total = str_replace(['$', '.', ' ', ','], ['', '', '', '.'], $l_total);

        // Busco datos para el array 'acara'
        $acaraNode = $getNodeValue("//td[contains(text(), 'A.C.A.R.A')]");
        list(, $a_porcentaje) = explode("(", $acaraNode);

        $a_porcentaje = trim(rtrim($a_porcentaje, "%)"));
        $a_porcentaje = rtrim(number_format((float)$a_porcentaje, 2, '.', ''), '.') ?: '0';

        $a_total = $getNodeValue("//td[contains(text(), 'A.C.A.R.A')]//following-sibling::td[1]");
        $a_total = str_replace(['$', '.', ' ', ','], ['', '', '', '.'], $a_total);

        // Busco datos para el array 'ente_cooperador_sugit' (Datos Ente Cooperador Ley 23283 - SUGIT)
        $enteCooperadorSugitNode = $getNodeValue("//td[contains(text(), 'Ente Cooperador Ley 23283 - SUGIT')]");
        list(, $ecs_porcentaje) = explode("(", $enteCooperadorSugitNode);

        $ecs_porcentaje = trim(rtrim($ecs_porcentaje, "%)"));
        $ecs_porcentaje = rtrim(number_format((float)$ecs_porcentaje, 2, '.', ''), '.') ?: '0';

        $ecs_total = $getNodeValue("//td[contains(text(), 'Ente Cooperador Ley 23283 - SUGIT')]//following-sibling::td[1]");
        $ecs_total = str_replace(['$', '.', ' ', ','], ['', '', '', '.'], $ecs_total);

        // Busco datos para el array 'ente_cooperador' (Datos Ente Cooperador Ley 23283)
        $enteCooperadorNode = $getNodeValue("//td[contains(text(), 'Ente Cooperador Ley 23283 (')]");
        list(, $ec_porcentaje) = explode("(", $enteCooperadorNode);

        $ec_porcentaje = trim(rtrim($ec_porcentaje, ") %)"));
        $ec_porcentaje = rtrim(number_format((float)$ec_porcentaje, 2, '.', ''), '.') ?: '0';

        $ec_total = $getNodeValue("//td[contains(text(), 'Ente Cooperador Ley 23283 (')]//following-sibling::td[1]");
        $ec_total = str_replace(['$', '.', ' ', ','], ['', '', '', '.'], $ec_total);

        // Busco dato 'total depositado'
        $totalDepositadoNode = $getNodeValue("//b[contains(text(), 'Total Depositado')]");
        $totalDepositadoParts = explode('$', $totalDepositadoNode);
        $totalDepositado = trim($totalDepositadoParts[1] ?? '');
        $totalDepositado = str_replace(['.', ','], ['', '.'], $totalDepositado);

        $data = [
            'file_upload_id' => $this->fileUploadId,
            'archivo' => $filename,
            'dependencia' => $fileNameParts[0] ?? null,
            'fecha' => $fileNameParts[1] ?? null,
            'tercero' => $fileNameParts[2] ?? null,
            'periodo_rendicion_inicio' => $periodoInicio,
            'periodo_rendicion_fin' => $periodoFin,
            'numero_de_rendicion' => $numeroRendicion,
            'fecha_hora_proceso' => $fechaHoraProceso,
            'banco' => $nombreBanco,
            'numero_cuenta_bancaria' => $numeroCuentaBancaria,
            'detalle_cuenta_bancaria' => $detalleCuentaBancaria,
            'cbu' => $cbu,
            'cuit' => $cuit,
            'municipalidad_provincia' => $getNodeValue("//b[contains(text(), '" . $municipalidadProvincia . "')]//following-sibling::text()[1]"),
            'total_infracciones' => $totalInfracciones,
            'gastos_bancarios_recaudadora' => $gastosBancariosRecaudadora,
            'subtotal' => $subtotal,
            'lugar_nombre' => $l_nombre,
            'lugar_porcentaje' => $l_porcentaje,
            'lugar_total' => $l_total,
            'acara_porcentaje' => $a_porcentaje,
            'acara_total' => $a_total,
            'ente_cooperador_sugit_porcentaje' => $ecs_porcentaje,
            'ente_cooperador_sugit_total' => $ecs_total,
            'ente_cooperador_porcentaje' => $ec_porcentaje,
            'ente_cooperador_total' => $ec_total,
            'total_depositado' => $totalDepositado,
        ];

        Htm::create($data);
        File::delete($this->filePath);
    }
}

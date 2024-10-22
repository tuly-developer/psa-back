<?php

namespace Database\Seeders;

use App\Models\FileUpload;
use Illuminate\Database\Seeder;

class FileUploadTypeOne extends Seeder
{
    public function run(): void
    {

        FileUpload::create([
            'type' => 1,
            'filename' => 'safit_2024-01-01 (Enero 24).xls',
            'url' => 'storage/safit/safit_2024-01-01 (Junio 24).xls',
            'extension' => 'xls',
            'status' => 1,
            'error' => null,
            'user_id' => 1,
        ]);

        FileUpload::create([
            'type' => 1,
            'filename' => 'safit_2024-02-02 (Febrero 24).xls',
            'url' => 'storage/safit/safit_2024-02-02 (Febrero 24).xls',
            'extension' => 'xls',
            'status' => 1,
            'error' => null,
            'user_id' => 1,
        ]);

        FileUpload::create([
            'type' => 1,
            'filename' => 'safit_2024-03-03 (Marzo 24).xls',
            'url' => 'storage/safit/safit_2024-03-03 (Marzo 24).xls',
            'extension' => 'xls',
            'status' => 0,
            'error' =>  null,
            'user_id' => 1,
        ]);

        FileUpload::create([
            'type' => 1,
            'filename' => 'safit_2024-04-04 (Abril 24).xls',
            'url' => 'storage/safit/safit_2024-04-04 (Abril 24).xls',
            'extension' => 'xls',
            'status' => 2,
            'error' => null,
            'user_id' => 1,
        ]);

        FileUpload::create([
            'type' => 1,
            'filename' => 'safit_2024-05-05 (Mayo 24).xls',
            'url' => 'storage/safit/safit_2024-05-05 (Mayo 24).xls',
            'extension' => 'xls',
            'status' => 3,
            'error' => 'Encabezados incorrectos',
            'user_id' => 1,
        ]);

        FileUpload::create([
            'type' => 1,
            'filename' => 'safit_2024-06-06 (Junio 24).xls',
            'url' => 'storage/safit/safit_2024-06-06 (Junio 24).xls',
            'extension' => 'xls',
            'status' => 1,
            'error' => null,
            'user_id' => 1,
        ]);

        FileUpload::create([
            'type' => 1,
            'filename' => 'safit_2024-07-07 (Julio 24).xls',
            'url' => 'storage/safit/safit_2024-07-07 (Julio 24).xls',
            'extension' => 'xls',
            'status' => 1,
            'error' => null,
            'user_id' => 1,
        ]);

        FileUpload::create([
            'type' => 1,
            'filename' => 'safit_2024-08-08 (Agosto 24).xls',
            'url' => 'storage/safit/safit_2024-08-08 (Agosto 24).xls',
            'extension' => 'xls',
            'status' => 0,
            'error' => null,
            'user_id' => 1,
        ]);

        FileUpload::create([
            'type' => 1,
            'filename' => 'safit_2024-09-09 (Septiembre 24).xls',
            'url' => 'storage/safit/safit_2024-09-09 (Septiembre 24).xls',
            'extension' => 'xls',
            'status' => 2,
            'error' => null,
            'user_id' => 1,
        ]);

        FileUpload::create([
            'type' => 1,
            'filename' => 'safit_2024-10-10 (Octubre 24).xls',
            'url' => 'storage/safit/safit_2024-10-10 (Octubre 24).xls',
            'extension' => 'xls',
            'status' => 3,
            'error' => 'Formato no válido',
            'user_id' => 1,
        ]);

        FileUpload::create([
            'type' => 1,
            'filename' => 'safit_2024-11-11 (Noviembre 24).xls',
            'url' => 'storage/safit/safit_2024-11-11 (Noviembre 24).xls',
            'extension' => 'xls',
            'status' => 2,
            'error' => null,
            'user_id' => 1,
        ]);

        FileUpload::create([
            'type' => 1,
            'filename' => 'safit_2024-12-12 (Diciembre 24).xls',
            'url' => 'storage/safit/safit_2024-12-12 (Diciembre 24).xls',
            'extension' => 'xls',
            'status' => 3,
            'error' => 'Formato no válido',
            'user_id' => 1,
        ]);
    }
}

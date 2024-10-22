<?php

namespace Database\Seeders;

use App\Models\FileUpload;
use Illuminate\Database\Seeder;

class FileUploadTypeThree extends Seeder
{
    public function run(): void
    {

        FileUpload::create([
            'type' => 3,
            'filename' => 'SUGIT_rendiciones_010120240300.tar.gz',
            'url' => 'storage/targz/SUGIT_rendiciones_010120240300.tar.gz',
            'extension' => 'xls',
            'status' => 1,
            'error' => null,
            'user_id' => 1,
        ]);

        FileUpload::create([
            'type' => 3,
            'filename' => 'SUGIT_rendiciones_020220240300.tar.gz',
            'url' => 'storage/targz/SUGIT_rendiciones_020220240300.tar.gz',
            'extension' => 'xls',
            'status' => 1,
            'error' => null,
            'user_id' => 1,
        ]);

        FileUpload::create([
            'type' => 3,
            'filename' => 'SUGIT_rendiciones_030320240300.tar.gz',
            'url' => 'storage/targz/SUGIT_rendiciones_030320240300.tar.gz',
            'extension' => 'xls',
            'status' => 0,
            'error' =>  null,
            'user_id' => 1,
        ]);

        FileUpload::create([
            'type' => 3,
            'filename' => 'SUGIT_rendiciones_040420240300.tar.gz',
            'url' => 'storage/targz/SUGIT_rendiciones_040420240300.tar.gz',
            'extension' => 'xls',
            'status' => 2,
            'error' => null,
            'user_id' => 1,
        ]);

        FileUpload::create([
            'type' => 3,
            'filename' => 'SUGIT_rendiciones_050520240300.tar.gz',
            'url' => 'storage/targz/SUGIT_rendiciones_050520240300.tar.gz',
            'extension' => 'xls',
            'status' => 3,
            'error' => 'Encabezados incorrectos',
            'user_id' => 1,
        ]);

        FileUpload::create([
            'type' => 3,
            'filename' => 'SUGIT_rendiciones_060620240300.tar.gz',
            'url' => 'storage/targz/SUGIT_rendiciones_060620240300.tar.gz',
            'extension' => 'xls',
            'status' => 1,
            'error' => null,
            'user_id' => 1,
        ]);
        
        FileUpload::create([
            'type' => 3,
            'filename' => 'SUGIT_rendiciones_070720240300.tar.gz',
            'url' => 'storage/targz/SUGIT_rendiciones_070720240300.tar.gz',
            'extension' => 'xls',
            'status' => 1,
            'error' => null,
            'user_id' => 1,
        ]);
        
        FileUpload::create([
            'type' => 3,
            'filename' => 'SUGIT_rendiciones_080820240300.tar.gz',
            'url' => 'storage/targz/SUGIT_rendiciones_080820240300.tar.gz',
            'extension' => 'xls',
            'status' => 0,
            'error' => null,
            'user_id' => 1,
        ]);
        
        FileUpload::create([
            'type' => 3,
            'filename' => 'SUGIT_rendiciones_090920240300.tar.gz',
            'url' => 'storage/targz/SUGIT_rendiciones_090920240300.tar.gz',
            'extension' => 'xls',
            'status' => 2,
            'error' => null,
            'user_id' => 1,
        ]);
        
        FileUpload::create([
            'type' => 3,
            'filename' => 'SUGIT_rendiciones_101020240300.tar.gz',
            'url' => 'storage/targz/SUGIT_rendiciones_101020240300.tar.gz',
            'extension' => 'xls',
            'status' => 3,
            'error' => 'Formato no válido',
            'user_id' => 1,
        ]);

        FileUpload::create([
            'type' => 3,
            'filename' => 'SUGIT_rendiciones_111120240300.tar.gz',
            'url' => 'storage/targz/SUGIT_rendiciones_111120240300.tar.gz',
            'extension' => 'xls',
            'status' => 2,
            'error' => null,
            'user_id' => 1,
        ]);
        
        FileUpload::create([
            'type' => 3,
            'filename' => 'SUGIT_rendiciones_121220240300.tar.gz',
            'url' => 'storage/targz/SUGIT_rendiciones_121220240300.tar.gz',
            'extension' => 'xls',
            'status' => 3,
            'error' => 'Formato no válido',
            'user_id' => 1,
        ]);
    }
}

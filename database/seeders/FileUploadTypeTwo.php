<?php

namespace Database\Seeders;

use App\Models\FileUpload;
use Illuminate\Database\Seeder;

class FileUploadTypeTwo extends Seeder
{
    public function run(): void
    {

        FileUpload::create([
            'type' => 2,
            'filename' => 'Rendicion_Envio_00001.xls',
            'url' => 'storage/cobranzas/Rendicion_Envio_00001.xls',
            'extension' => 'xls',
            'status' => 1,
            'error' => null,
            'user_id' => 1,
        ]);

        FileUpload::create([
            'type' => 2,
            'filename' => 'Rendicion_Envio_00002.xls',
            'url' => 'storage/cobranzas/Rendicion_Envio_00002.xls',
            'extension' => 'xls',
            'status' => 1,
            'error' => null,
            'user_id' => 1,
        ]);

        FileUpload::create([
            'type' => 2,
            'filename' => 'Rendicion_Envio_00003.xls',
            'url' => 'storage/cobranzas/Rendicion_Envio_00003.xls',
            'extension' => 'xls',
            'status' => 0,
            'error' =>  null,
            'user_id' => 1,
        ]);

        FileUpload::create([
            'type' => 2,
            'filename' => 'Rendicion_Envio_00004.xls',
            'url' => 'storage/cobranzas/Rendicion_Envio_00004.xls.xlsx',
            'extension' => 'xls',
            'status' => 2,
            'error' => null,
            'user_id' => 1,
        ]);

        FileUpload::create([
            'type' => 2,
            'filename' => 'Rendicion_Envio_00005.xls',
            'url' => 'storage/cobranzas/Rendicion_Envio_00005.xls',
            'extension' => 'xls',
            'status' => 3,
            'error' => 'Encabezados incorrectos',
            'user_id' => 1,
        ]);

        FileUpload::create([
            'type' => 2,
            'filename' => 'Rendicion_Envio_00006.xls',
            'url' => 'storage/cobranzas/Rendicion_Envio_00006.xls',
            'extension' => 'xls',
            'status' => 1,
            'error' => null,
            'user_id' => 1,
        ]);
        
        FileUpload::create([
            'type' => 2,
            'filename' => 'Rendicion_Envio_00007.xls',
            'url' => 'storage/cobranzas/Rendicion_Envio_00007.xls',
            'extension' => 'xls',
            'status' => 1,
            'error' => null,
            'user_id' => 1,
        ]);
        
        FileUpload::create([
            'type' => 2,
            'filename' => 'Rendicion_Envio_00008.xls',
            'url' => 'storage/cobranzas/Rendicion_Envio_00008.xls',
            'extension' => 'xls',
            'status' => 0,
            'error' => null,
            'user_id' => 1,
        ]);
        
        FileUpload::create([
            'type' => 2,
            'filename' => 'Rendicion_Envio_00009.xls',
            'url' => 'storage/cobranzas/Rendicion_Envio_00009.xls',
            'extension' => 'xls',
            'status' => 2,
            'error' => null,
            'user_id' => 1,
        ]);
        
        FileUpload::create([
            'type' => 2,
            'filename' => 'Rendicion_Envio_00010.xls',
            'url' => 'storage/cobranzas/Rendicion_Envio_00010.xls',
            'extension' => 'xls',
            'status' => 3,
            'error' => 'Formato no válido',
            'user_id' => 1,
        ]);

        FileUpload::create([
            'type' => 2,
            'filename' => 'Rendicion_Envio_00011.xls',
            'url' => 'storage/cobranzas/Rendicion_Envio_00011.xls',
            'extension' => 'xls',
            'status' => 2,
            'error' => null,
            'user_id' => 1,
        ]);
        
        FileUpload::create([
            'type' => 2,
            'filename' => 'Rendicion_Envio_00012.xls',
            'url' => 'storage/cobranzas/Rendicion_Envio_00012.xls',
            'extension' => 'xls',
            'status' => 3,
            'error' => 'Formato no válido',
            'user_id' => 1,
        ]);
        
    }
}

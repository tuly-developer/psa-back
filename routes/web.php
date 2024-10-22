<?php

use App\Models\Htm;
use App\Models\Safit;
use App\Models\Cobranza;
use App\Models\FileUpload;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/upload', function () {
    return view('upload');
});

Route::get('/phpinfo', function () {
    return phpinfo();
});

Route::get('/delete-dump-files', function () {
    File::deleteDirectory(storage_path('app/extracted'));
    File::deleteDirectory(storage_path('app/public/safit'));
    File::deleteDirectory(storage_path('app/public/cobranzas'));
    File::deleteDirectory(storage_path('app/public/targz'));
    File::deleteDirectory(storage_path('app/temp'));
    return "Directorio basuras eliminado exitosamente.";
});

Route::get('/storagelink', function () {
    try {
        Artisan::call('storage:link');
        return 'Storage link creado correctamente.';
    } catch (\Exception $e) {
        return 'Error al crear el storage link: ' . $e->getMessage();
    }
});

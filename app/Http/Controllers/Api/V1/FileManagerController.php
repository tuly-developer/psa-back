<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Htm;
use App\Models\FileUpload;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Enums\FileStatusEnum;
use App\Jobs\ProcessXlsFileJob;
use App\Jobs\ProcessSafitFileJob;
use App\Jobs\ProcessTargzFileJob;
use App\Jobs\ProcessPaymentFileJob;
use App\Constants\DatabaseConstants;
use App\Enums\FileTypeEnum;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class FileManagerController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'perPage' => ['nullable', 'int'],
            'orderBy' => ['nullable', 'string'],
            'orderByMethod' => ['nullable', 'string'],
            'status' => ['nullable', 'int'],
            'type' => ['required', 'int'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ]);

        $query = FileUpload::query();
        $query->where('type', FileTypeEnum::from($validated['type'])->value);

        if (isset($validated['status'])) $query->where('status', $validated['status']);
        if (isset($validated['date_from'])) $query->whereDate('created_at', '>=', $validated['date_from']);
        if (isset($validated['date_to'])) $query->whereDate('created_at', '<=', $validated['date_to']);

        $query->orderBy($validated['orderBy'] ?? DatabaseConstants::ORDER_BY, $validated['orderByMethod'] ?? DatabaseConstants::ORDER_BY_METHOD);
        $results = $query->paginate($validated['perPage'] ?? DatabaseConstants::PER_PAGE);

        return response()->json($results);
    }

    //* YA FUNCIONA (SAFIT - DIARIO)
    public function processSafitFile(Request $request)
    {
        $file = $request->file('file');

        $fileName = $file->getClientOriginalName();
        if (!Str::contains($fileName, 'safit')) return response()->json(['error' => 'El archivo no contiene la palabra "safit" en su nombre. Por favor verifique que sea el archivo correcto'], 400);

        $existingFile = FileUpload::where('filename', $fileName)->where('status', FileStatusEnum::COMPLETED->value)->first();
        if ($existingFile) return response()->json(['error' => 'El archivo: ' . $existingFile->filename . ' fue subido el: ' . $existingFile->created_at], 400);

        $filePath = $file->storeAs('public/safit', $fileName);

        $userId = auth()->id() ?? null;

        $fileUploadId = FileUpload::create([
            'filename' => $fileName,
            'type' => FileTypeEnum::SAFIT,
            'url' => env('APP_URL') . '/storage/safit/' . $fileName,
            'extension' => $file->getClientOriginalExtension(),
            'status' => FileStatusEnum::QUEUED,
            'user_id' => $userId
        ])->id;

        ProcessSafitFileJob::dispatch($filePath, $fileUploadId, $userId);

        return response()->json(['message' => 'El archivo se ha cargado con éxito y se procesará en breve, este proceso puede demorar dependiendo del tamaño del archivo.', 200]);
    }

    //* YA FUNCIONA (COBRANZAS - SEMANAL)
    public function uploadPaymentFile(Request $request)
    {
        $file = $request->file('file');

        $fileName = $file->getClientOriginalName();
        if (!Str::contains($fileName, 'Rendicion')) return response()->json(['error' => 'El archivo no contiene la palabra "Rendicion" en su nombre. Por favor verifique que sea el archivo correcto'], 400);

        $existingFile = FileUpload::where('filename', $fileName)->where('status', FileStatusEnum::COMPLETED->value)->first();
        if ($existingFile) return response()->json(['error' => 'El archivo: ' . $existingFile->filename . ' fue subido el: ' . $existingFile->created_at], 400);

        $userId = auth()->id() ?? null;

        $fileUploadId = FileUpload::create([
            'filename' => $fileName,
            'type' => FileTypeEnum::COBRANZA,
            'url' => env('APP_URL') . '/storage/cobranzas/' . $fileName,
            'extension' => $file->getClientOriginalExtension(),
            'status' => FileStatusEnum::QUEUED,
            'user_id' => $userId
        ])->id;

        $response = Http::attach(
            'file',
            file_get_contents($file->getPathname()),
            $file->getClientOriginalName()
        )->post(env('FILE_CONVERTER_URL') . '/v1/modelos/xlsx/sugit', [
            'userId' => $userId,
            'fileUploadId' => $fileUploadId
        ]);

        if ($response->failed()) return response()->json(['message' => 'No se pudo cargar el archivo.', 'error' => $response->body()], 500);

        return response()->json(['message' => 'El archivo se ha cargado con éxito y se procesará en breve, este proceso puede demorar dependiendo del tamaño del archivo.', 200]);
    }

    //* YA FUNCIONA (COBRANZAS - SEMANAL)
    public function processPaymentFile(Request $request)
    {
        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $filePath = $file->storeAs('public/cobranzas', $fileName);

        FileUpload::find($request->fileUploadId)->update([
            'url' => env('APP_URL') . '/storage/cobranzas/' . $fileName,
            'extension' => $file->getClientOriginalExtension(),
        ]);

        ProcessPaymentFileJob::dispatch($filePath, $request->fileUploadId, $request->userId);

        return response()->json(['message' => 'El archivo se ha cargado con éxito y se procesará en breve, este proceso puede demorar dependiendo del tamaño del archivo.', 200]);
    }

    //* YA FUNCIONA (RENDICIONES - MENSUAL)
    public function uploadTargzFile(Request $request)
    {
        set_time_limit(300);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        if ($extension != 'gz') return response()->json(['message' => 'Tipo de archivo no compatible.'], 400);

        $fileName = $file->getClientOriginalName();
        if (!Str::contains($fileName, 'SUGIT')) return response()->json(['error' => 'El archivo no contiene la palabra "SUGIT" en su nombre. Por favor verifique que sea el archivo correcto'], 400);

        $existingFile = FileUpload::where('filename', $fileName)->where('status', FileStatusEnum::COMPLETED->value)->first();
        if ($existingFile) return response()->json(['error' => 'El archivo: ' . $existingFile->filename . ' fue subido el: ' . $existingFile->created_at], 400);

        $userId = auth()->id() ?? null;

        $fileUploadId = FileUpload::create([
            'filename' => $fileName,
            'type' => FileTypeEnum::TARGZ,
            'url' => env('APP_URL') . '/storage/targz/' . $fileName,
            'extension' => $file->getClientOriginalExtension(),
            'status' => FileStatusEnum::QUEUED,
            'user_id' => $userId
        ])->id;

        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $uniqueName = Str::uuid()->toString();
        $filePath = storage_path('app/temp/');
        $file->move($filePath, $uniqueName . '.tar.gz');

        ProcessTargzFileJob::dispatch($filePath, $originalName, $fileUploadId, $userId, $uniqueName);

        return response()->json(['message' => 'El archivo se ha cargado con éxito y se procesará en breve, este proceso puede demorar dependiendo del tamaño del archivo.'], 200);
    }

    //* YA FUNCIONA (RENDICIONES - MENSUAL)
    public function processXlsInsideTargzFile(Request $request)
    {
        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $filePath = $file->storeAs('public/targz/xls/' . $request->fileName, $fileName);

        ProcessXlsFileJob::dispatch($filePath, $fileName, $request->fileUploadId, $request->userId);

        return response()->noContent();
    }
}

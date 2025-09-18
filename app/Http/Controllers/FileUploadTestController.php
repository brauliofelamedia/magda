<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileUploadTestController extends Controller
{
    public function test()
    {
        // Crear directorio temporal si no existe
        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0775, true);
        }
        
        // Información del entorno
        $info = [
            'PHP version' => phpversion(),
            'Laravel version' => app()->version(),
            'Storage path' => storage_path(),
            'Public path' => public_path(),
            'Base path' => base_path(),
            'Default filesystem disk' => config('filesystems.default'),
            'Is temp dir writable' => is_writable(storage_path('app/temp')),
            'Storage URL' => asset('storage'),
        ];

        // Intentar crear un archivo de prueba
        $testContent = 'Test file content: ' . date('Y-m-d H:i:s');
        $testFilename = 'test_' . time() . '.txt';
        $testPath = 'temp/' . $testFilename;
        
        try {
            // Asegurarse que la carpeta existe
            if (!is_dir(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0775, true);
                $info['Created temp directory'] = 'Yes';
            }
            
            // Probar almacenamiento con Storage
            $stored = Storage::put($testPath, $testContent);
            $info['Storage::put result'] = $stored ? 'Success' : 'Failed';
            $info['Storage::exists result'] = Storage::exists($testPath) ? 'Yes' : 'No';
            
            // Verificar el archivo creado
            $fullPath = storage_path('app/' . $testPath);
            $info['Full path'] = $fullPath;
            $info['File exists'] = file_exists($fullPath) ? 'Yes' : 'No';
            
            if (file_exists($fullPath)) {
                $info['File content'] = file_get_contents($fullPath);
            }
            
            // Probar con file_put_contents
            $directPath = storage_path('app/temp/direct_' . $testFilename);
            $directResult = file_put_contents($directPath, $testContent);
            $info['Direct file write result'] = $directResult ? 'Success' : 'Failed';
            $info['Direct file exists'] = file_exists($directPath) ? 'Yes' : 'No';
        } catch (\Exception $e) {
            $info['Error'] = $e->getMessage();
        }

        return view('file-upload-test', ['info' => $info]);
    }
    
    public function testManual(Request $request)
    {
        $result = [];
        
        try {
            $request->validate([
                'file' => 'required|file'
            ]);
            
            $file = $request->file('file');
            $result['original_name'] = $file->getClientOriginalName();
            $result['mime_type'] = $file->getMimeType();
            $result['size'] = $file->getSize();
            $result['real_path'] = $file->getRealPath();
            
            // Método 1: Almacenar con store()
            $path1 = $file->store('temp');
            $result['store_path'] = $path1;
            $result['store_exists'] = Storage::exists($path1) ? 'Yes' : 'No';
            $result['store_full_path'] = storage_path('app/' . $path1);
            $result['store_file_exists'] = file_exists(storage_path('app/' . $path1)) ? 'Yes' : 'No';
            
            // Método 2: Almacenar con storeAs()
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $path2 = $file->storeAs('temp', $filename);
            $result['storeAs_path'] = $path2;
            $result['storeAs_exists'] = Storage::exists($path2) ? 'Yes' : 'No';
            $result['storeAs_full_path'] = storage_path('app/' . $path2);
            $result['storeAs_file_exists'] = file_exists(storage_path('app/' . $path2)) ? 'Yes' : 'No';
            
            // Método 3: Mover manualmente
            $tempDir = storage_path('app/temp');
            $customFilename = 'manual_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path3 = 'temp/' . $customFilename;
            $fullPath3 = $tempDir . '/' . $customFilename;
            $file->move($tempDir, $customFilename);
            $result['manual_path'] = $path3;
            $result['manual_full_path'] = $fullPath3;
            $result['manual_file_exists'] = file_exists($fullPath3) ? 'Yes' : 'No';
            
            // Método 4: Usar Storage::putFileAs
            $customFilename4 = 'storage_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path4 = Storage::putFileAs('temp', $file, $customFilename4);
            $result['storage_path'] = $path4;
            $result['storage_exists'] = Storage::exists($path4) ? 'Yes' : 'No';
            $result['storage_full_path'] = storage_path('app/' . $path4);
            $result['storage_file_exists'] = file_exists(storage_path('app/' . $path4)) ? 'Yes' : 'No';
            
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
        }
        
        return redirect()->route('upload.test')->with('test_result', $result);
    }
}
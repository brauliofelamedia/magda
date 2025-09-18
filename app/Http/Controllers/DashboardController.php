<?php

namespace App\Http\Controllers;

use App\DataTables\UsersDataTable;
use App\Imports\UsersImport;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\APICalls;
use App\Models\Notification;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    use APICalls;

    public function index()
    {
        return redirect()->route('login');
    }

    public function syncUsers()
    {
        //Obtenemos los usuarios del sistema y de la aplicación
        $respondents = $this->getRespondents();
        $users = User::get();

        foreach($respondents as $respondent){

            $checkUser = User::where('email',$respondent['node']['email'])->first();

            if(!$checkUser){
                $user = new User();
                $user->name = $respondent['node']['firstName'].' '.$respondent['node']['lastName'];
                $user->email = $respondent['node']['email'];
                $user->account_id = $respondent['node']['id'];
                $user->lang = $respondent['node']['locale'] ?? 'es-ES';
                $user->platform = true;
                $user->password = bcrypt('password');
                $user->assignRole('respondent');
                $user->user_id = 4;
                $user->save();
            }

        }

        return redirect()->back()->with('success','Se ha sincronizado los usuarios.');
    }

    public function welcome(){

        if(Auth::user()->hasRole('respondent')){
            return view('dashboard.assessments.welcome');
        }

        $users = User::query()
            ->when(request('search'), function($query) {
                $query->where('name', 'like', '%' . request('search') . '%')
                      ->orWhere('email', 'like', '%' . request('search') . '%');
            })
            ->when(request('category'), function($query) {
                $query->where('category_id', request('category'));
            });

        if(Auth::user()->hasRole('administrator')){
            $users = $users;
        } else if(Auth::user()->hasRole('institution')){
            $users = $users->where('user_id', Auth::user()->id)->role(['respondent']);
        }

        $users = $users->paginate(10)->onEachSide(1);
        
        $locales = config('languages.locales');
        $institutions = User::whereHas('roles', function ($query) {
            $query->where('name', 'institution');
        })->get();

        $categories = Category::where('user_id',Auth::user()->id)->get();

        return view('dashboard.index',compact('users','locales','institutions','categories'));
    }

    public function import()
    {
        return view('dashboard.import');
    }
    
    public function import_process(Request $request)
    {
        \Log::info('Iniciando proceso de importación');
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);
        
        // Almacenar el archivo temporalmente
        $file = $request->file('file');
        
        // Asegurar que la carpeta temporal exista
        $tempDir = storage_path('app'.DIRECTORY_SEPARATOR.'temp');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0775, true);
        }
        
        // Generar nombre único para el archivo
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        $path = 'temp/' . $filename;
        
        // Mover el archivo manualmente para asegurar que se guarde correctamente
        $file->move($tempDir, $filename);
        
        // Guardar la ruta en la sesión para usarla después
        session(['temp_excel_file' => $path]);
        
        // Asegurar que los separadores de directorios sean consistentes (importante en Windows)
        $fullPath = str_replace('/', DIRECTORY_SEPARATOR, storage_path('app'.DIRECTORY_SEPARATOR.$path));
        
        // Verificar si el archivo existe antes de procesarlo
        \Log::info('Verificando archivo en ruta: ' . $fullPath);
        if (!file_exists($fullPath)) {
            \Log::warning('Archivo no encontrado en: ' . $fullPath);
            
            // Intentar crear una copia en la carpeta temporal
            try {
                \Log::info('Intentando crear una copia del archivo');
                if (!is_dir(dirname($fullPath))) {
                    \Log::info('Creando directorio: ' . dirname($fullPath));
                    mkdir(dirname($fullPath), 0775, true);
                }
                \Log::info('Copiando archivo desde: ' . $file->getRealPath() . ' a: ' . $fullPath);
                copy($file->getRealPath(), $fullPath);
            } catch (\Exception $e) {
                \Log::error('Error al copiar archivo: ' . $e->getMessage());
                return back()->with('error', 'No se pudo procesar el archivo. Error: ' . $e->getMessage());
            }
            
            // Verificar nuevamente
            if (!file_exists($fullPath)) {
                \Log::error('Archivo sigue sin existir después de intentar copiarlo');
                return back()->with('error', 'No se pudo procesar el archivo. El archivo no se encuentra en: ' . $fullPath);
            } else {
                \Log::info('Archivo copiado correctamente');
            }
        } else {
            \Log::info('Archivo encontrado correctamente');
        }
        
        // Leer las cabeceras del archivo Excel
        try {
            \Log::info('Intentando leer cabeceras del archivo Excel: ' . $fullPath);
            $headers = Excel::toArray([], $fullPath)[0][0];
            \Log::info('Cabeceras leídas correctamente', ['headers' => $headers]);
        } catch (\Exception $e) {
            \Log::error('Error al leer archivo Excel: ' . $e->getMessage());
            return back()->with('error', 'Error al leer el archivo Excel: ' . $e->getMessage());
        }
        
        $requiredColumns = [
            'name' => 'Nombre',
            'last_name' => 'Apellidos',
            'email' => 'Correo electrónico',
            'gender' => 'Género',
            'lang' => 'Idioma preferido',
            'password' => 'Contraseña (opcional)'
        ];
        
        // Si es administrador, añadir las columnas de rol y correo de institución
        if (Auth::user()->hasRole('administrator')) {
            $requiredColumns['role'] = 'Rol';
            $requiredColumns['user_id'] = 'Correo de Institución (opcional)';
        }
        
        return view('dashboard.import_mapping', [
            'headers' => $headers,
            'required_columns' => $requiredColumns
        ]);
    }
    
    public function import_map(Request $request)
    {
        // Validar que se han proporcionado los mapeos necesarios
        $validationRules = [
            'mapping.name' => 'required|numeric',
            'mapping.last_name' => 'required|numeric',
            'mapping.email' => 'required|numeric',
            'mapping.gender' => 'required|numeric',
            'mapping.lang' => 'required|numeric',
        ];
        
        // Si es administrador, validar también los campos adicionales
        if (Auth::user()->hasRole('administrator')) {
            $validationRules['mapping.role'] = 'required|numeric';
            // user_id es opcional
        }
        
        $request->validate($validationRules);
        
        // Obtener el archivo de la sesión
        $path = session('temp_excel_file');
        
        if (!$path) {
            return redirect()->route('dashboard.import')->with('error', 'El archivo ha expirado. Por favor, cargue el archivo nuevamente.');
        }
        
        // Asegurar que los separadores de directorios sean consistentes (importante en Windows)
        $fullPath = str_replace('/', DIRECTORY_SEPARATOR, storage_path('app'.DIRECTORY_SEPARATOR.$path));
        
        // Verificar que el archivo existe
        if (!file_exists($fullPath)) {
            return redirect()->route('dashboard.import')->with('error', 'El archivo no se encuentra. Por favor, cargue el archivo nuevamente.');
        }
        
        // Importar con el mapeo personalizado y obtener el resumen
        $import = new UsersImport($request->mapping);
        $import->import($fullPath);
        
        // Obtener estadísticas
        $importedCount = count($import->imported);
        $existingCount = count($import->existing);
        $failedCount = count($import->failed);
        
        // Guardar el resumen en la sesión para mostrarlo en una vista
        session()->flash('import_summary', [
            'imported' => $import->imported,
            'existing' => $import->existing,
            'failed' => $import->failed,
            'stats' => [
                'imported' => $importedCount,
                'existing' => $existingCount,
                'failed' => $failedCount,
                'total' => $importedCount + $existingCount + $failedCount
            ]
        ]);
        
        // Eliminar el archivo temporal y la sesión del archivo
        \Storage::delete($path);
        session()->forget('temp_excel_file');
        
        // Redirigir con mensaje adecuado según los resultados
        $message = "Resumen de importación: {$importedCount} usuarios importados, {$existingCount} usuarios ya existentes, {$failedCount} fallidos.";
        
        if ($importedCount > 0 && $failedCount == 0) {
            return redirect()->route('dashboard.import.summary')->with('success', $message);
        } elseif ($importedCount > 0 && $failedCount > 0) {
            return redirect()->route('dashboard.import.summary')->with('warning', $message);
        } elseif ($importedCount == 0 && $failedCount == 0) {
            return redirect()->route('dashboard.import.summary')->with('info', 'No se importaron nuevos usuarios. Todos los usuarios ya existían en el sistema.');
        } else {
            return redirect()->route('dashboard.import.summary')->with('error', $message);
        }
    }

    public function remove_notification(Request $request)
    {
        if($request->remove == true){
            $users = User::where('user_id',Auth::user()->id)->get();
            $userIds = $users->pluck('id')->toArray();
            $notification = Notification::whereIn('user_id', $userIds)
                            ->where('status', false)
                            ->update(['status' => true]);
            return redirect()->back()->with('success', 'Se han eliminado las notificaciones');
        }
    }
    
    /**
     * Muestra el resumen de la importación de usuarios
     */
    public function import_summary()
    {
        // Obtener el resumen de la importación desde la sesión
        $summary = session('import_summary');
        
        // Si no hay resumen, redirigir a la página de importación
        if (!$summary) {
            return redirect()->route('dashboard.import')->with('error', 'No hay información de importación disponible.');
        }
        
        return view('dashboard.import.summary', [
            'summary' => $summary
        ]);
    }
    
    /**
     * Determina qué plantilla descargar según el rol del usuario.
     */
    public function download_template()
    {
        $user = \Auth::user();
        
        if ($user->hasRole('administrator')) {
            return $this->download_admin_template();
        } else {
            return $this->download_institution_template();
        }
    }
    
    /**
     * Descarga la plantilla para administradores con todos los campos.
     */
    public function download_admin_template()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Configurar encabezados para administradores
        $headers = ['Nombre', 'Apellidos', 'Correo', 'Idioma', 'Rol', 'Género', 'Contraseña (opcional)', 'Correo de Institución (opcional)'];
        $lastCol = 'H';
        
        $sheet->fromArray([$headers], NULL, 'A1');
        
        // Dar formato a los encabezados
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '033A60'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray($headerStyle);
        
        // Agregar datos de ejemplo para administradores
        $exampleData = [
            ['Juan', 'Pérez González', 'juan.perez@example.com', 'es', 'respondent', 'male', 'password123', 'institucion@ejemplo.com'],
            ['María', 'López Sánchez', 'maria.lopez@example.com', 'en', 'institution', 'female', '', 'otra.institucion@ejemplo.com'],
        ];
        $sheet->fromArray($exampleData, NULL, 'A2');
        
        // Ajustar ancho de columnas
        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Crear el archivo Excel
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'plantilla_importacion_usuarios_admin.xlsx';
        $tempPath = storage_path('app/public/' . $filename);
        $writer->save($tempPath);
        
        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }
    
    /**
     * Descarga la plantilla para instituciones con campos limitados.
     */
    public function download_institution_template()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Configurar encabezados para instituciones
        $headers = ['Nombre', 'Apellidos', 'Correo', 'Idioma', 'Género', 'Contraseña (opcional)'];
        $lastCol = 'F';
        
        $sheet->fromArray([$headers], NULL, 'A1');
        
        // Dar formato a los encabezados
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '033A60'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray($headerStyle);
        
        // Agregar datos de ejemplo para instituciones
        $exampleData = [
            ['Juan', 'Pérez González', 'juan.perez@example.com', 'es', 'male', 'password123'],
            ['María', 'López Sánchez', 'maria.lopez@example.com', 'en', 'female', ''],
        ];
        $sheet->fromArray($exampleData, NULL, 'A2');
        
        // Ajustar ancho de columnas
        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Crear el archivo Excel
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'plantilla_importacion_usuarios_institucion.xlsx';
        $tempPath = storage_path('app/public/' . $filename);
        $writer->save($tempPath);
        
        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }

    public function superLink($emails,$idTemplate){
        return $this->sendSuperLink($emails,$idTemplate);
    }
}

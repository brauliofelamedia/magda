<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Mail\SendCreateUser;
use App\Traits\APICalls;
use Illuminate\Support\Facades\Auth;

class UsersImport implements ToModel, WithStartRow
{
    use APICalls;
    use Importable;
    
    private $mapping;
    public $imported = [];
    public $existing = [];
    public $failed = [];
    
    /**
     * Constructor.
     *
     * @param array $mapping Mapeo de columnas (nombre de campo => índice en Excel)
     */
    public function __construct(?array $mapping = null)
    {
        $this->mapping = $mapping;
    }
    
    public function model(array $row)
    {
        // Si no hay mapeo, usar los índices predeterminados
        if (!$this->mapping) {
            $nameIndex = 0;
            $lastNameIndex = 1;
            $emailIndex = 2;
            $langIndex = 3;
            $roleIndex = 4; // Para administradores
            $passwordIndex = 5; // Si hay contraseña
            $genderIndex = 6; // Si hay género
            $userIdIndex = 7; // Para asignar a institución
            $typeOfEvaluationIndex = null;
        } else {
            $nameIndex = $this->mapping['name'];
            $lastNameIndex = $this->mapping['last_name'];
            $emailIndex = $this->mapping['email'];
            $langIndex = $this->mapping['lang'];
            $genderIndex = $this->mapping['gender'];
            $passwordIndex = (isset($this->mapping['password']) && $this->mapping['password'] !== '') ? (int)$this->mapping['password'] : null;
            $roleIndex = (isset($this->mapping['role']) && $this->mapping['role'] !== '') ? (int)$this->mapping['role'] : null;
            $userIdIndex = (isset($this->mapping['user_id']) && $this->mapping['user_id'] !== '') ? (int)$this->mapping['user_id'] : null;
            $typeOfEvaluationIndex = (isset($this->mapping['type_of_evaluation']) && $this->mapping['type_of_evaluation'] !== '') ? (int)$this->mapping['type_of_evaluation'] : null;
        }
        
        // Verificar si hay datos en la fila
        if (empty($row[$emailIndex])) {
            return null; // Saltar filas vacías
        }
        
        // Verificar si el usuario ya existe
        $userExist = User::where('email', trim($row[$emailIndex]))->first();
        if ($userExist) {
            // Registrar usuario existente para el resumen
            $this->existing[] = [
                'email' => $row[$emailIndex],
                'name' => isset($row[$nameIndex]) ? $row[$nameIndex] : 'N/A',
                'row' => $this->getRowCount()
            ];
            return null; // No importar usuarios existentes
        }
        
        // Generar o usar contraseña proporcionada
        if ($passwordIndex !== null && !empty($row[$passwordIndex])) {
            $passwordText = $row[$passwordIndex];
            $password = Hash::make($row[$passwordIndex]);
        } else {
            $randomString = Str::random(10);
            $passwordText = $randomString;
            $password = Hash::make($randomString);
        }
        
        // Determinar el idioma preferido
        $lang = !empty($row[$langIndex]) ? $row[$langIndex] : 'es';
        
        // Determinar el género y convertir a formato requerido (F/M)
        $gender = !empty($row[$genderIndex]) ? $row[$genderIndex] : 'male';
        // Convertir "female" a "F" y "male" a "M"
        if (strtolower($gender) === 'female') {
            $gender = 'F';
        } elseif (strtolower($gender) === 'male') {
            $gender = 'M';
        }
        
        // Determinar el ID de usuario institucional (si aplica)
        $institutionId = null;
        
        if (Auth::user()->hasRole('administrator') && $userIdIndex !== null && !empty($row[$userIdIndex])) {
            // Solo administradores pueden asignar a otras instituciones
            $institutionEmail = trim($row[$userIdIndex]);
            $targetInstitution = User::where('email', $institutionEmail)->first();
            
            // Verificar si se encontró la institución y tiene el rol adecuado
            if ($targetInstitution && $targetInstitution->hasRole('institution')) {
                $institutionId = $targetInstitution->id;
                \Log::info("Usuario asignado a institución: {$institutionEmail} (ID: {$institutionId})");
            } else {
                \Log::warning("No se encontró institución con email: {$institutionEmail} o no tiene el rol adecuado");
            }
        } elseif (Auth::user()->hasRole('institution')) {
            // Instituciones siempre asignan a sí mismas
            $institutionId = Auth::id();
        }
        
        // Crear nuevo usuario en el sistema
        $userData = [
            'name' => $row[$nameIndex],
            'last_name' => $row[$lastNameIndex],
            'email' => trim($row[$emailIndex]),
            'lang' => $lang,
            'password' => $password,
        ];
        
        if ($institutionId !== null) {
            $userData['user_id'] = $institutionId;
        }

        // Determinar tipo de evaluación
        $resolvedEvalType = null;

        // 1) Buscar en la columna mapeada explícitamente
        if ($typeOfEvaluationIndex !== null && !empty($row[$typeOfEvaluationIndex])) {
            $resolvedEvalType = strtolower(trim($row[$typeOfEvaluationIndex]));
        }

        // 2) Si no fue mapeado o vino vacío, escanear todas las celdas buscando el valor
        if (!$resolvedEvalType) {
            foreach ($row as $cell) {
                $cellVal = strtolower(trim((string)$cell));
                if (in_array($cellVal, ['resumida', 'short', 'completa', 'long'])) {
                    $resolvedEvalType = $cellVal;
                    break;
                }
            }
        }

        // Normalizar a los valores internos del sistema: 'short' y 'long'
        $evalMap = [
            'resumida' => 'short',
            'short'    => 'short',
            'completa' => 'long',
            'long'     => 'long',
        ];

        // 3) Guardar si se encontró un valor válido
        if ($resolvedEvalType && isset($evalMap[$resolvedEvalType])) {
            $internalVal = $evalMap[$resolvedEvalType];
            $userData['type_of_evaluation'] = [$internalVal];
            \Log::info("type_of_evaluation asignado: {$internalVal} para {$row[$emailIndex]}");
        }

        // 4) Si sigue sin valor, heredar de la institución asignada
        if (!isset($userData['type_of_evaluation']) && $institutionId) {
            $institution = User::find($institutionId);
            if ($institution && !empty($institution->type_of_evaluation)) {
                $userData['type_of_evaluation'] = $institution->type_of_evaluation;
                \Log::info("type_of_evaluation heredado de institución para {$row[$emailIndex]}");
            }
        }

        $user = new User($userData);
        
        // Guardar el usuario para obtener su ID
        $user->save();
        
        // Determinar qué rol asignar
        if (Auth::user()->hasRole('administrator') && $roleIndex !== null && !empty($row[$roleIndex])) {
            // Si es administrador, puede asignar cualquier rol especificado en el Excel
            $user->assignRole($row[$roleIndex]);
        } else {
            // Si es institución o cualquier otro usuario, siempre asigna "respondent"
            $user->assignRole('respondent');
        }
        
        // Crear usuario remoto si es necesario
        $data = $this->createUser(
            $row[$nameIndex],
            $row[$lastNameIndex],
            trim($row[$emailIndex]),
            $gender,
            $lang
        );

        try {
            $accountId = null;

            if (isset($data['data']['createRespondent'])) {
                // Creación exitosa en la API
                $accountId = $data['data']['createRespondent']['respondent']['id'];
                $user->account_id = $accountId;
                $user->save();
            } elseif (isset($data['errors'])) {
                // La API retornó un error — verificar si el respondent ya existe
                $apiError = $data['errors'][0]['message'] ?? '';
                \Log::warning("API error al crear respondent '{$user->email}': {$apiError}");

                if (stripos($apiError, 'already exists') !== false) {
                    // El respondent existe en gr8pi — buscar su ID para vincularlo localmente
                    $accountId = $this->findRespondentIdByEmail(trim($row[$emailIndex]));
                    if ($accountId) {
                        $user->account_id = $accountId;
                        $user->save();
                        \Log::info("Respondent existente vinculado: {$user->email} -> account_id {$accountId}");
                    } else {
                        \Log::warning("No se pudo recuperar el account_id del respondent existente: {$user->email}");
                    }
                }
            }

            // Enviar correo de notificación independientemente del resultado de la API
            try {
                \Log::info("Intentando enviar correo a: {$user->email} con password: {$passwordText}");
                Mail::to($user->email)->send(new SendCreateUser($user, $passwordText));
                \Log::info("Correo enviado correctamente a: {$user->email}");
            } catch (\Exception $mailException) {
                \Log::error("Error al enviar correo: {$mailException->getMessage()}");
            }

            // Registrar usuario importado exitosamente
            $this->imported[] = [
                'email' => $user->email,
                'name' => $user->name,
                'password' => $passwordText,
                'row' => $this->getRowCount()
            ];

            if (!$accountId) {
                \Log::warning("Usuario importado localmente pero sin account_id remoto: {$user->email}");
            }

        } catch (\Exception $e) {
            // Registrar cualquier error en el proceso
            $this->failed[] = [
                'email' => $row[$emailIndex],
                'name' => $row[$nameIndex],
                'row' => $this->getRowCount(),
                'error' => $e->getMessage()
            ];
            \Log::error("Error al importar usuario: {$e->getMessage()}");
            
            // Intentar eliminar el usuario si se creó
            if (isset($user->id)) {
                $user->delete();
            }
        }
        
        return null; // Ya guardamos manualmente el usuario
    }

    public function startRow(): int
    {
        return 2;
    }
    
    /**
     * Obtiene el número de fila actual (considerando la fila de inicio)
     */
    private function getRowCount(): int
    {
        static $rowCount = 0;
        $rowCount++;
        return $rowCount + $this->startRow() - 1;
    }
}

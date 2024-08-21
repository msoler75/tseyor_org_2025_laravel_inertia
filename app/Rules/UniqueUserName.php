<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class UniqueUserName implements ValidationRule
{

    protected $userId;

    public function __construct($userId = null)
    {
        $this->userId = $userId;
    }

    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        // Normaliza el nombre eliminando acentos y convirtiendo a minúsculas
        $normalizedValue = $this->normalize($value);

        // Obtén todos los nombres de usuario de la base de datos
        $users = DB::table('users')
        ->where('id', '!=', $this->userId)
        ->select('name')
        ->get();

        foreach ($users as $user) {
            $normalizedUserName = $this->normalize($user->name);
            \Log::info("compara [$normalizedValue] con [$normalizedUserName]");
            if ($normalizedUserName === $normalizedValue) {
                $fail('El nombre de usuario ya está en uso.');
                break;
            }
        }
    }

    private function normalize($value)
    {
        // Mapeo de caracteres acentuados a sus equivalentes sin acento
        $unwanted_array = [
            'Á'=>'A', 'À'=>'A', 'Â'=>'A', 'Ä'=>'A', 'Ã'=>'A', 'Å'=>'A',
            'á'=>'a', 'à'=>'a', 'â'=>'a', 'ä'=>'a', 'ã'=>'a', 'å'=>'a',
            'É'=>'E', 'È'=>'E', 'Ê'=>'E', 'Ë'=>'E',
            'é'=>'e', 'è'=>'e', 'ê'=>'e', 'ë'=>'e',
            'Í'=>'I', 'Ì'=>'I', 'Î'=>'I', 'Ï'=>'I',
            'í'=>'i', 'ì'=>'i', 'î'=>'i', 'ï'=>'i',
            'Ó'=>'O', 'Ò'=>'O', 'Ô'=>'O', 'Ö'=>'O', 'Õ'=>'O',
            'ó'=>'o', 'ò'=>'o', 'ô'=>'o', 'ö'=>'o', 'õ'=>'o',
            'Ú'=>'U', 'Ù'=>'U', 'Û'=>'U', 'Ü'=>'U',
            'ú'=>'u', 'ù'=>'u', 'û'=>'u', 'ü'=>'u',
            'Ñ'=>'N', 'ñ'=>'n', 'Ç'=>'C', 'ç'=>'c'
        ];

       // Reemplaza caracteres acentuados y convierte a minúsculas
       $str = strtolower(strtr($value, $unwanted_array));

       // Reemplaza múltiples espacios en blanco con un solo espacio
       $str = preg_replace('/\s+/', ' ', $str);

       // Elimina espacios al principio y al final
       return trim($str);
    }
}

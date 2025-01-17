<?php

namespace App\Pigmalion;

use Illuminate\Support\Facades\Log;


define('FORCE_RENAME_COPYING', 0);

class RenameHelper
{

    /**
     * Renombra un archivo o carpeta que está en una carpeta $folder de viejo nombre $oldName a $newName
     **/



    /**
     * Renombra un archivo o una carpeta
     * @param mixed $source  Ruta absoluta
     * @param mixed $destination  Ruta absoluta
     * @return bool
     */
    public static function safe_rename($source, $destination)
    {
        Log::info("safe_rename($source, $destination)");

        if (!file_exists($source)) {
            Log::error("El archivo $source no existe");
            return false;
        }

        // Verifica si el destino es una subcarpeta del origen
        if (strpos(realpath($destination), realpath($source) . DIRECTORY_SEPARATOR) === 0) {
            Log::error("El destino es una subcarpeta del origen. Operación no permitida.");
            return false;
        }

        // Intenta usar primero Storage
        if (!FORCE_RENAME_COPYING && StorageItem::move(StorageItem::fromPath($source)->location, StorageItem::fromPath($destination)->location)) {
            Log::info("Se ha movido con StorageItem::move($source, $destination)");
            return true;
        }

        // Intenta renombrar directamente
        if (!FORCE_RENAME_COPYING && @rename($source, $destination)) {
            Log::info("Se ha renombrado con rename($source, $destination)");
            return true;
        }

        // Si falla, procedemos con el método de copia
        $isFile = is_file($source);
        $nodo = null;

        // intento de copiar atómico
        // Fase de copia al destino
        $nodo = \App\Models\Nodo::create([
            'ubicacion' => StorageItem::fromPath($destination)->location,
            'oculto' => 1
        ]);
        if ($isFile) {
            if (!@copy($source, $destination)) {
                Log::error("Fallo al copiar el archivo: $source");
                $nodo->forceDelete();
                return false;
            }
        } else {
            if (!self::copyRecursive($source, $destination, $source)) {
                Log::error("Fallo al copiar la carpeta: $source");
                self::cleanup($destination);
                $nodo->forceDelete();
                return false;
            }
        }


        // Eliminar el original solo después de un commit exitoso
        if ($isFile) {
            if (!@unlink($source)) {
                Log::error("Fallo al eliminar el archivo original: $source");
                return false;
            }
        } else {
            if (!self::cleanup($source)) {
                Log::error("Fallo al eliminar el directorio original: $source");
                return false;
            }
        }

        $nodo->forceDelete();

        return true;
    }
    private static function copyRecursive($source, $destination, $baseSource)
    {
        Log::info('Copiando ' . $source . ' a ' . $destination);
        if (!is_dir($destination)) {
            if (!mkdir($destination, 0777, true)) {
                return false;
            }
        }

        $dir = opendir($source);

        while (($file = readdir($dir)) !== false) {
            if ($file != '.' && $file != '..') {
                $srcFile = $source . '/' . $file;
                $destFile = $destination . '/' . $file;

                // Solo copiar si el archivo o directorio está dentro del directorio base
                if (strpos($srcFile, $baseSource) === 0) {
                    if (is_dir($srcFile)) {
                        if (!self::copyRecursive($srcFile, $destFile, $baseSource)) {
                            closedir($dir);
                            return false;
                        }
                    } else {
                        if (!copy($srcFile, $destFile)) {
                            closedir($dir);
                            return false;
                        }
                    }
                }
            }
        }

        closedir($dir);
        return true;
    }

    private static function cleanup($path)
    {
        if (is_file($path)) {
            return unlink($path);
        }

        if (is_dir($path)) {
            $files = array_diff(scandir($path), ['.', '..']);
            foreach ($files as $file) {
                self::cleanup($path . '/' . $file);
            }
            return rmdir($path);
        }

        return false;
    }
}

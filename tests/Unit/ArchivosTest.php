<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Pigmalion\StorageItem;
use App\Pigmalion\RenameHelper;
use Illuminate\Support\Facades\Log;

class ArchivosTest extends TestCase
{


    protected function creaArchivo($ubicacion)
    {
        $s = new StorageItem($ubicacion);
        $path = $s->path;
        // comprueba si existe
        if (file_exists($path)) {
            $this->assertFalse(is_dir($path), "Ya existe $ubicacion pero no es un archivo");
            return;
        }
        // crea un archivo vacio
        file_put_contents($path, "");
    }


    protected function creaCarpeta($ubicacion)
    {
        $s = new StorageItem($ubicacion);
        $path = $s->path;
        // comprueba si existe la carpeta en $path
        if (file_exists($path)) {
            $this->assertTrue(is_dir($path), "Ya existe $ubicacion pero no es una carpeta");
            return;
        }
        // crea una carpeta
        mkdir($path, 0777, true);
    }



    private function borrar($ubicacion)
    {
        $s = new StorageItem($ubicacion);
        $path = $s->path;

        if (is_file($path)) {
            return unlink($path);
        }

        if (is_dir($path)) {
            $files = array_diff(scandir($path), ['.', '..']);
            foreach ($files as $file) {
                $s = StorageItem::fromPath($path . '/' . $file);
                $this->borrar($s->location);
            }
            return rmdir($path);
        }

        return false;
    }

    // testea el rename de un archivo y de una carpeta con archivos y subcarpetas dentro
    public function test_rename()
    {
        // preparamos origen
        $this->creaCarpeta("/archivos/test1");
        $this->creaCarpeta("/archivos/test1/sub1");
        $this->creaCarpeta("/archivos/test1/sub2");
        $this->creaArchivo("/archivos/test1/sub1/archivo1.txt");

        // preparamos destino
        $this->borrar("/archivos/test1ren");

        $source = (new StorageItem("/archivos/test1"))->path;
        $destination = (new StorageItem("/archivos/test1ren"))->path;

        $this->assertTrue(file_exists($source));
        $this->assertFalse(file_exists($destination));

        RenameHelper::safe_rename($source, $destination);

        // comprobar si se ha renombrado bien
        // no existe carpeta original
        $this->assertFalse(file_exists((new StorageItem("/archivos/test1"))->path));
        // sí existe carpeta destino y todos sus contenidos
        $this->assertTrue(file_exists((new StorageItem("/archivos/test1ren"))->path));
        $this->assertTrue(file_exists((new StorageItem("/archivos/test1ren/sub1"))->path));
        $this->assertTrue(file_exists((new StorageItem("/archivos/test1ren/sub2"))->path));
        $this->assertTrue(file_exists((new StorageItem("/archivos/test1ren/sub1/archivo1.txt"))->path));
    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Pigmalion\StorageItem;

function normalizeDir($dir)
{
    return str_replace('\\', '/', $dir);
}

class StorageItemTest extends TestCase
{

    public function testArchivos1_()
    {
        $dir = new StorageItem('/archivos/carpeta1/n.jpg');
        $this->assertEquals('/archivos/carpeta1/n.jpg', $dir->location);
        $this->assertEquals('archivos/carpeta1/n.jpg', $dir->relativeLocation);
        $this->assertEquals('archivos', $dir->disk);
        $this->assertEquals('http://localhost/archivos/carpeta1/n.jpg', $dir->url);
        $this->assertEquals(normalizeDir(storage_path('app') . '/archivos/carpeta1/n.jpg'), normalizeDir($dir->path));
    }


    public function testMedios1_()
    {
        $dir = new StorageItem('/almacen/medios/equipos/1');
        $this->assertEquals('/almacen/medios/equipos/1', $dir->location);
        $this->assertEquals('medios/equipos/1', $dir->relativeLocation);
        $this->assertEquals('public', $dir->disk);
        $this->assertEquals('http://localhost/almacen/medios/equipos/1', $dir->url);
        $this->assertEquals(normalizeDir(storage_path() . '/app/public/medios/equipos/1'), normalizeDir($dir->path));
    }

    public function testArchivos2_()
    {
        $dir = StorageItem::fromUrl('http://localhost/archivos/carpeta1/n.jpg');
        $this->assertEquals("/archivos/carpeta1/n.jpg", $dir->location);

        $dir = StorageItem::fromPath(storage_path('app') . '/archivos/carpeta1/n.jpg');
        $this->assertEquals("/archivos/carpeta1/n.jpg", $dir->location);
    }

    public function testMedios2_()
    {
        $dir = StorageItem::fromUrl('http://localhost/almacen/medios/equipos/1');
        $this->assertEquals("/almacen/medios/equipos/1", $dir->location);

        $dir = StorageItem::fromPath(storage_path('app/public') . '/medios/equipos/1');
        $this->assertEquals("/almacen/medios/equipos/1", $dir->location);
    }
}

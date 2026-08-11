<?php

namespace Tests\Unit;

use App\Pigmalion\StorageItem;
use Tests\TestCase;

function normalizeDir($dir)
{
    return str_replace('\\', '/', $dir);
}

class StorageItemTest extends TestCase
{
    public function test_archivos1_()
    {
        $dir = new StorageItem('/archivos/carpeta1/n.jpg');
        $this->assertEquals('/archivos/carpeta1/n.jpg', $dir->location);
        $this->assertEquals('archivos/carpeta1/n.jpg', $dir->relativeLocation);
        $this->assertEquals('archivos', $dir->disk);
        $this->assertEquals('http://localhost/archivos/carpeta1/n.jpg', $dir->url);
        $this->assertEquals(normalizeDir(storage_path('app').'/archivos/carpeta1/n.jpg'), normalizeDir($dir->path));
    }

    public function test_medios1_()
    {
        $dir = new StorageItem('/almacen/medios/equipos/1');
        $this->assertEquals('/almacen/medios/equipos/1', $dir->location);
        $this->assertEquals('medios/equipos/1', $dir->relativeLocation);
        $this->assertEquals('public', $dir->disk);
        $this->assertEquals('http://localhost/almacen/medios/equipos/1', $dir->url);
        $this->assertEquals(normalizeDir(storage_path().'/app/public/medios/equipos/1'), normalizeDir($dir->path));
    }

    public function test_archivos2_()
    {
        $dir = StorageItem::fromUrl('http://localhost/archivos/carpeta1/n.jpg');
        $this->assertEquals('/archivos/carpeta1/n.jpg', $dir->location);

        $dir = StorageItem::fromPath(storage_path('app').'/archivos/carpeta1/n.jpg');
        $this->assertEquals('/archivos/carpeta1/n.jpg', $dir->location);
    }

    public function test_medios2_()
    {
        $dir = StorageItem::fromUrl('http://localhost/almacen/medios/equipos/1');
        $this->assertEquals('/almacen/medios/equipos/1', $dir->location);

        $dir = StorageItem::fromPath(storage_path('app/public').'/medios/equipos/1');
        $this->assertEquals('/almacen/medios/equipos/1', $dir->location);
    }
}

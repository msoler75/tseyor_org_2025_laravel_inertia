<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\ContenidoConAudios;
use Laravel\Scout\Searchable;

class Comunicado extends ContenidoConAudios
{
    use CrudTrait;
    use Searchable;

    protected $fillable = [
        'titulo',
        'numero',
        'categoria',
        'descripcion',
        'texto',
        'imagen',
        'fecha_comunicado',
        'ano',
        'visibilidad',
        'pdf',
        'audios',
        'slug',
    ];

    protected $dates = [
        'fechaComunicado',
    ];


    public $sortable = [
        'fechaComunicado'
    ];

    // hooks del modelo
    /*
    protected static function booted()
    {
        // cuando se guarda el item
        static::saving(function ($comunicado) {
            // $comunicado->slug = "heidi2";
        });
    }
    */

    /* public static function search($term)
    {
        return static::query()
            ->where('titulo', 'LIKE', "%{$term}%")
            ->orWhere('texto', 'LIKE', "%{$term}%")
            ->orWhere('numero', '=', "{$term}");
    }
    */



    // ContenidoConAudios

    /**
     * Nombre de los archivos de audio
     **/
    public function generarNombreAudio($index)
    {
        $tipo = "";
        switch (intval($this->categoria)) {
            case 1:
                $tipo = " TAP";
                break;
            case 2:
                $tipo = " DDM";
                break;
            case 3:
                $tipo = " MUUL";
                break;
        }

        $fecha = date('ymd', strtotime($this->fecha_comunicado));
        $audios = $this->obtenerAudiosArray();
        $multiple = count($audios) > 1;
        return "TSEYOR $fecha ({$this->numero})" . $tipo . ($multiple ? " " . ('a' + $index) : "") . ".mp3";
    }

    /**
     * En qué carpeta se guardarán los audios
     **/
    public function generarRutaAudios()
    {
        $año = date('Y', strtotime($this->fecha_comunicado));
        return "media/comunicados/audios/$año";
    }


    // ACCESSORS

    public function getCategoriaNombreAttribute()
    {
        switch ($this->categoria) {
            case 1:
                return "TAP";
            case 2:
                return "12 del M.";
            case 3:
                return "Muul";
            default:
                "General";
        }
    }

    // uploads_multiple
    // https://backpackforlaravel.com/docs/5.x/crud-fields#upload_multiple
    /*
    public function setAudiosAttribute($value)
    {
        // dd($this->num, $this->fecha_comunicado , $this->tipo == null);
        // solo se puede subir archivos de audios o pdf si el número, la fecha y el tipo de comuniacdo están establecidos
        if (!$this->num || !$this->fecha_comunicado || $this->tipo)
            return;

        $attribute_name = "audios";
        $disk = "public";
        $destination_path = "media/comunicados/mp3";

        // $this->uploadMultipleFilesToDisk($value, $attribute_name, $disk, $destination_path);

        $originalModelValue = $this->getOriginal()[$attribute_name] ?? [];

        if (!is_array($originalModelValue)) {
            $attribute_value = json_decode($originalModelValue, true) ?? [];
        } else {
            $attribute_value = $originalModelValue;
        }

        $files_to_clear = request()->get('clear_' . $attribute_name);

        // if a file has been marked for removal,
        // delete it from the disk and from the db
        if ($files_to_clear) {
            foreach ($files_to_clear as $key => $filename) {
                Storage::disk($disk)->delete($filename);
                $attribute_value = Arr::where($attribute_value, function ($value, $key) use ($filename) {
                    return $value != $filename;
                });
            }
        }


        // if a new file is uploaded, store it on disk and its filename in the database
        if (request()->hasFile($attribute_name)) {
            $idx = 0;
            $multiple = count(request()->file($attribute_name)) > 0;
            foreach (request()->file($attribute_name) as $file) {
                if ($file->isValid()) {
                    // 1. Generate a new file name
                    $fecha = date('ymd', strtotime($this->fecha_comunicado));
                    $tipo = "";
                    switch ($this->categoria) {
                        case 1:
                            $tipo = "TAP";
                            break;
                        case 2:
                            $tipo = "DDM";
                            break;
                        case 3:
                            $tipo = "MUUL";
                            break;
                    }

                    $new_file_name = "TSEYOR $fecha ($this->num)" . $tipo . ($multiple ? " " . ('a' + $idx) : "") . ".mp3"; //$file->getClientOriginalExtension();

                    // to-do: conversión audio y metadata de mp3

                    // 2. Move the new file to the correct path
                    $file_path = $file->storeAs($destination_path, $new_file_name, $disk);

                    // 3. Add the public path to the database
                    $attribute_value[] = $file_path;
                }
            }
        }

        $this->attributes[$attribute_name] = json_encode($attribute_value);
    }

    protected $casts = [
        'audios' => 'array'
    ];


    // https://backpackforlaravel.com/docs/5.x/crud-fields#upload
    public function setPdfAttribute($value)
    {
        // solo se puede subir archivos de audios o pdf si el número, la fecha y el tipo de comuniacdo están establecidos
        if (!$this->num || !$this->fecha_comunicado || $this->tipo == null)
            return;

        $attribute_name = "pdf";
        $disk = "public";
        $destination_path = "media/comunicados/pdf";
        $fileName = null;

        //$this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path, $fileName = null);

        // if a new file is uploaded, delete the previous file from the disk
        if (
            request()->hasFile($attribute_name) &&
            $this->{$attribute_name} &&
            $this->{$attribute_name} != null
        ) {
            Storage::disk($disk)->delete($this->{$attribute_name});
            $this->attributes[$attribute_name] = null;
        }

        // if the file input is empty, delete the file from the disk
        if (is_null($value) && $this->{$attribute_name} != null) {
            Storage::disk($disk)->delete($this->{$attribute_name});
            $this->attributes[$attribute_name] = null;
        }

        // if a new file is uploaded, store it on disk and its filename in the database
        if (request()->hasFile($attribute_name) && request()->file($attribute_name)->isValid()) {
            // 1. Generate a new file name
            $file = request()->file($attribute_name);

            // use the provided file name or generate a random one
            //$new_file_name = $fileName ?? md5($file->getClientOriginalName().random_int(1, 9999).time()).'.'.$file->getClientOriginalExtension();

            // 1. Generate a new file name
            $fecha = date('ymd', strtotime($this->fecha_comunicado));
            $tipo = "";
            switch ($this->categoria) {
                case 1:
                    $tipo = "TAP";
                    break;
                case 2:
                    $tipo = "DDM";
                    break;
                case 3:
                    $tipo = "MUUL";
                    break;
            }

            $new_file_name = "TSEYOR $fecha ($this->num)" . $tipo . $file->getClientOriginalExtension();

            // 2. Move the new file to the correct path
            $file_path = $file->storeAs($destination_path, $new_file_name, $disk);

            // 3. Save the complete path to the database
            $this->attributes[$attribute_name] = $file_path;
        }

        // return $this->attributes[{$attribute_name}]; // uncomment if this is a translatable field
    }
    */

    // SCOUT


    /**
     * Solo se indexa si acaso está publicado
     */
    public function shouldBeSearchable(): bool
    {
        return $this->visibilidad == 'P';
    }


    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            // <- Always include the primary key
            'title' => $this->titulo,
            'description' => $this->descripcion,
            'content' => $this->texto,
            'categoria' => $this->categoria,
            'numero' => $this->numero,
            // 'fecha_comunicado' => $this->fecha_comunicado,
            'ano' => $this->ano
        ];
    }
}

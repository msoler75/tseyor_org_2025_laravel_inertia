<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use App\Models\Comunicado;
use App\Rules\DropzoneRule;
use App\Pigmalion\StorageItem;

// https://github.com/jargoud/laravel-backpack-dropzone

class StorePsicografiaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // Obtener el ID del registro actual si está presente
        $psicografiaId = $this->input('id');

        $rules = [
            'titulo' => 'required|min:2',
            'slug' => ['nullable', 'regex:/^[a-z0-9\-]+$/', \Illuminate\Validation\Rule::unique('psicografias', 'slug')->ignore($psicografiaId)],
            'descripcion' => 'required|max:65000',
            'imagen' => 'file|mimes:jpeg,jpg,webp,png|max:4096',
        ];

        return $rules;
    }


    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'slug.regex' => 'El slug solo puede contener letras minúsculas, números y guiones.',
        ];
    }


    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $psicografiaId = $this->route('id');
            $imagen = $this->file('imagen');

            if ($psicografiaId) {
                // Estamos editando una psicografía existente
                $psicografia = \App\Models\Psicografia::find($psicografiaId);
                $imagenPrevia = $psicografia->imagen;
                $newImagen = $_REQUEST['imagen'] ?? null;
                $deletedImagen = isset($_REQUEST['imagen']) && $newImagen === "";

                if ($deletedImagen) {
                    // hemos borrado la imagen
                    if (empty($imagen))
                        $validator->errors()->add('imagen', 'Debes proporcionar un archivo de imagen.');
                } else {

                    $imagenLoc = $imagenPrevia;

                    $loc = null;
                    if ($imagenLoc)
                        $loc = new StorageItem($imagenLoc);

                    $imagenExistente = $imagenPrevia && $loc->exists();

                    if (empty($imagen) && !$imagenExistente)
                        $validator->errors()->add('imagen', 'Debes proporcionar un archivo de imagen.');
                }
            } else {
                // Estamos creando una nueva psicografia
                if (empty($imagen))
                    $validator->errors()->add('imagen', 'Debes proporcionar un archivo de imagen.');
            }
        });
    }
}

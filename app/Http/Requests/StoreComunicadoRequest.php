<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use App\Models\Comunicado;
use App\Rules\DropzoneRule;

// https://github.com/jargoud/laravel-backpack-dropzone

class StoreComunicadoRequest extends FormRequest
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
        $comunicadoId = $this->input('id');

        return [
            'titulo' => 'required|min:7|max:255',
            'slug' => [ 'nullable', 'regex:/^[a-z0-9\-]+$/', \Illuminate\Validation\Rule::unique('comunicados', 'slug')->ignore($comunicadoId) ],
            'texto' => 'required',
            'numero' => 'required|numeric|min:1|max:99999',
            'categoria' => 'required',
            'fecha_comunicado' => 'required',
            'descripcion' => 'max:400',
            'audios' => [
                'array',
            ],
            /* 'audios.*' => [
                new DropzoneRule("audios", ['audio']),
            ], */
            // 'pdf' => 'max:20000|mimes:pdf',
        ];
    }



    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $num = $this->input('numero');
            $categoria = $this->input('categoria');
            $comunicadoId = $this->route('id');

            $existingComunicado = Comunicado::where('numero', $num)
                ->where('categoria', $categoria);

            if (!empty ($comunicadoId))
                $existingComunicado->where('id', '!=', $comunicadoId); // Excluir el comunicado actual en caso de actualización

            $existingComunicado = $existingComunicado->exists();

            if ($existingComunicado)
                $validator->errors()->add('numero', 'Ya existe otro comunicado con este número y categoría');
        });
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
}

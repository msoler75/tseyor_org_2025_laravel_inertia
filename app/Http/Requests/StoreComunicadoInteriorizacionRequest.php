<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreComunicadoInteriorizacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $comunicadoId = $this->input('id');

        return [
            'titulo' => 'required|min:7|max:255',
            'slug' => ['nullable', 'regex:/^[a-z0-9\-]+$/', Rule::unique('comunicados_interiorizacion', 'slug')->ignore($comunicadoId)],
            'texto' => 'required',
            'nivel' => 'required|in:1,2',
            'ciclo' => 'nullable|string|max:100',
            'numero' => 'nullable|string|max:20',
            'fecha_comunicado' => 'required|date',
            'descripcion' => 'required|max:400',
            'audios' => ['array'],
            'visibilidad' => 'required|in:P,B',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $numero = $this->input('numero');
            $nivel = $this->input('nivel');
            $ciclo = $this->input('ciclo');
            $comunicadoId = $this->route('id');

            if ($numero) {
                $exists = \App\Models\ComunicadoInteriorizacion::where('numero', $numero)
                    ->where('nivel', $nivel)
                    ->where('ciclo', $ciclo);

                if ($comunicadoId) {
                    $exists->where('id', '!=', $comunicadoId);
                }

                if ($exists->exists()) {
                    $validator->errors()->add('numero', 'Ya existe otro comunicado con este número en el mismo nivel y ciclo');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'slug.regex' => 'El slug solo puede contener letras minúsculas, números y guiones.',
            'nivel.required' => 'El nivel es obligatorio (1 o 2).',
            'nivel.in' => 'El nivel debe ser 1 o 2.',
        ];
    }
}

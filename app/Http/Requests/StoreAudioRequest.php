<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use App\Models\Comunicado;
use App\Rules\DropzoneRule;

// https://github.com/jargoud/laravel-backpack-dropzone

class StoreAudioRequest extends FormRequest
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
        $audioId = $this->input('id');

        $rules = [
            'titulo' => 'required|min:8',
            'slug' => [
                \Illuminate\Validation\Rule::unique('audios', 'slug')->ignore($audioId),
            ],
            'audio' => 'nullable|file|mimes:mp3',
            'enlace' => 'nullable|url'
        ];

        return $rules;
    }



    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $audioId = $this->route('id');
            $mp3 = $this->file('audio');
            $enlace = $this->input('enlace');

            if ($audioId) {
                // Estamos editando un Audio existente
                $audio = \App\Models\Audio::find($audioId);
                $mp3Existente = $audio->audio;

                if (empty($enlace) && empty($mp3) && empty($mp3Existente)) {
                    $validator->errors()->add('audio', 'Debes proporcionar un enlace o un archivo de audio.');
                }
            } else {
                // Estamos creando un nuevo Audio
                if (empty($enlace) && empty($mp3)) {
                    $validator->errors()->add('audio', 'Debes proporcionar un enlace o un archivo de audio.');
                }
            }
        });
    }
}

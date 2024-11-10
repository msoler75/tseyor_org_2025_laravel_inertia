<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use App\Models\Comunicado;
use App\Rules\DropzoneRule;
use App\Pigmalion\StorageItem;

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
            'slug' => [ 'nullable', 'regex:/^[a-z0-9\-]+$/', \Illuminate\Validation\Rule::unique('audios', 'slug')->ignore($audioId)],
            'audio' => 'nullable|file|mimes:mp3',
            'enlace' => 'nullable|url'
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
            $audioId = $this->route('id');
            $mp3 = $this->file('audio');
            $enlace = $this->input('enlace');

            if ($audioId) {
                // Estamos editando un Audio existente
                $audio = \App\Models\Audio::find($audioId);
                $mp3Previo = $audio->audio;
                $newAudio = $_REQUEST['audio'] ?? null;
                $deletedAudio = isset($_REQUEST['audio']) && $newAudio === "";
                $enlaceAmbos = isset($_REQUEST['enlace']) ? $enlace : $audio->enlace;
                $mp3Ambos = isset($_FILES["audio"]["name"]) ? "nuevo" : (isset($_REQUEST['audio']) ? $mp3 : $mp3Previo);

                if ($deletedAudio) {
                    // hemos borrado el audio mp3
                    if (empty($enlace) && empty($mp3))
                        $validator->errors()->add('audio', 'Debes proporcionar un enlace o un archivo de audio.');
                } else {

                    $audioLoc = $mp3Previo;

                    $loc = null;
                    if ($audioLoc)
                        $loc = new StorageItem($audioLoc);

                    $mp3Existente = $mp3Previo && $loc->exists();

                    if (empty($enlace) && empty($mp3) && !$mp3Existente)
                        $validator->errors()->add('audio', 'Debes proporcionar un enlace o un archivo de audio.');


                    if ($enlaceAmbos && $mp3Ambos)
                        $validator->errors()->add('audio', 'Debes proporcionar un enlace O un archivo de audio. Pero no ambos.');
                }
            } else {
                // Estamos creando un nuevo Audio
                if (empty($enlace) && empty($mp3))
                    $validator->errors()->add('audio', 'Debes proporcionar un enlace o un archivo de audio.');

                if (!empty($enlace) && !empty($mp3))
                    $validator->errors()->add('audio', 'Debes proporcionar un enlace O un archivo de audio. Pero no ambos.');
            }
        });
    }
}

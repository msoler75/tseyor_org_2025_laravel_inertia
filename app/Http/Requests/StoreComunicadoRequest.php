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
        return [
            'titulo' => 'required|min:7|max:255',
            'texto' => 'required',
            'numero' => 'required|numeric|min:1|max:9999',
            'categoria' => 'required',
            'fecha_comunicado' => 'required',
            'descripcion' => 'max:400',
            'audios' => [
                'array',
            ],
            /* 'audios.*' => [
                new DropzoneRule("audios", ['audio']),
            ], */
            'pdf' => 'max:20000|mimes:pdf',
        ];
    }


    public function after(): array
    {
        // dd($this->input('visibilidad'), $this->input('categoria'), !$this->input('numero'), $this->input('fecha_comunicado'));
        // dd($this->file('audios'));
        $comunicado = null;
        if ($this->input('id')) {
            $comunicado = Comunicado::find($this->input('id'));
        }
        return [
            function (Validator $validator) use ($comunicado) {
                if ($this->input('visibilidad') == 'P') {

                    if ((!$comunicado || !$comunicado->pdf) && !$this->file('pdf')) {
                        $validator->errors()->add(
                            'pdf',
                            'Pdf es requerido para publicar el comunicado'
                        );
                    }
                }
            }
        ];
    }
}

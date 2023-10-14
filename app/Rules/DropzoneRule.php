<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

// desde librería Jargoud
class DropzoneRule implements ValidationRule
{
    /**
     * @inheritDoc
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $disk = Storage::disk(
            config('dropzone.storage.destination_disk')
        );

        if (!$disk->exists($value)) {
            $fail("Archivo no encontrado");
        }

        if (!empty($this->mimeTypes) && !in_array($disk->mimeType($value), $this->mimeTypes))
            $fail("Tipo de archivo no admitido");

        /*if (!empty($this->isExistingUrlCallback)) {
            $callback = $this->isExistingUrlCallback;
            return $callback($value);
        }*/

        $publicRelativePath = Str::replaceFirst(url("/"), "", $value);
        $publicAbsolutePath = public_path($publicRelativePath);

        if (!file_exists($publicAbsolutePath))
            $fail("Archivo no encontrado...");
    }

    /*
    public function message(): string
    {
        return trans(
            'validation.mimetypes',
            [
                'values' => implode(',', $this->mimeTypes),
            ]
        );
    }
    */
}

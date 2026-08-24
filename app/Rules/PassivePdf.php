<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class PassivePdf implements ValidationRule
{
    private const ACTIVE_CONTENT_PATTERN = '/\/(?:EmbeddedFile|JavaScript|JS|Launch|RichMedia)\b/i';

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile || ! $value->isValid()) {
            return;
        }

        $path = $value->getRealPath();
        $contents = is_string($path) ? file_get_contents($path) : false;

        if (! is_string($contents) || ! str_starts_with($contents, '%PDF-')) {
            $fail('The :attribute must contain a valid PDF file signature.');

            return;
        }

        if (preg_match(self::ACTIVE_CONTENT_PATTERN, $contents) === 1) {
            $fail('The :attribute cannot contain scripts, embedded files, or launch actions.');
        }
    }
}

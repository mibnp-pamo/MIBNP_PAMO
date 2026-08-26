<?php

namespace App\Http\Requests;

use App\Models\NewsPost;
use App\Rules\PassivePdf;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StaffNewsPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_pinned' => $this->boolean('is_pinned'),
            'remove_image' => $this->boolean('remove_image'),
            'remove_document' => $this->boolean('remove_document'),
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:160'],
            'summary' => ['required', 'string', 'max:500'],
            'body' => ['nullable', 'string', 'max:30000'],
            'category' => ['required', 'string', Rule::in(NewsPost::CATEGORIES)],
            'source' => ['required', 'string', 'max:120'],
            'external_url' => ['nullable', 'url:http,https', 'max:2048'],
            'status' => ['required', Rule::in([NewsPost::STATUS_DRAFT, NewsPost::STATUS_PUBLISHED])],
            'is_pinned' => ['boolean'],
            'published_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date'],
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'image_alt' => ['nullable', 'string', 'max:255'],
            'remove_image' => ['boolean'],
            'document' => [
                'nullable',
                'file',
                'mimes:pdf',
                'extensions:pdf',
                'max:10240',
                new PassivePdf,
            ],
            'document_label' => ['nullable', 'string', 'max:160'],
            'remove_document' => ['boolean'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $newsPost = $this->route('newsPost');
                $hasExistingDocument = $newsPost instanceof NewsPost
                    && filled($newsPost->document_path)
                    && ! $this->boolean('remove_document');

                if (
                    ! $this->filled('body')
                    && ! $this->filled('external_url')
                    && ! $this->hasFile('document')
                    && ! $hasExistingDocument
                ) {
                    $validator->errors()->add(
                        'body',
                        'Provide a full update, an external source URL, or a public PDF document.',
                    );
                }

                if (
                    ! $this->filled('expires_at')
                    || $validator->errors()->hasAny(['published_at', 'expires_at'])
                ) {
                    return;
                }

                $publicationTime = $this->date('published_at') ?? now();
                $expirationTime = $this->date('expires_at');

                if ($expirationTime?->lessThanOrEqualTo($publicationTime)) {
                    $validator->errors()->add(
                        'expires_at',
                        'The expiration time must be later than the publication time.',
                    );
                }
            },
        ];
    }
}

<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\StaffNewsPostRequest;
use App\Models\NewsPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NewsPostController extends Controller
{
    public function index(): View
    {
        return view('staff.news.index', [
            'newsPosts' => NewsPost::query()
                ->with('author')
                ->orderByDesc('is_pinned')
                ->orderByDesc('published_at')
                ->orderByDesc('updated_at')
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('staff.news.create', [
            'newsPost' => new NewsPost([
                'source' => 'MIBNP PAMO',
                'status' => NewsPost::STATUS_DRAFT,
                'published_at' => now(),
            ]),
            'categories' => NewsPost::CATEGORIES,
        ]);
    }

    public function store(StaffNewsPostRequest $request): RedirectResponse
    {
        $data = $this->postData($request);
        $data['slug'] = $this->uniqueSlug($data['title']);
        $data['created_by'] = $request->user()->id;

        try {
            if ($request->hasFile('image')) {
                $data['image_path'] = $this->storeImage($request->file('image'));
                $data['image_asset'] = null;
            }

            if ($request->hasFile('document')) {
                $this->addDocumentData($data, $request->file('document'));
            }

            $newsPost = NewsPost::query()->create($data);
        } catch (\Throwable $exception) {
            if (! empty($data['image_path'])) {
                Storage::disk('public')->delete($data['image_path']);
            }

            if (! empty($data['document_path'])) {
                Storage::disk('local')->delete($data['document_path']);
            }

            throw $exception;
        }

        return redirect()
            ->route('staff.news.edit', $newsPost)
            ->with('status', 'The news update was created.');
    }

    public function edit(NewsPost $newsPost): View
    {
        return view('staff.news.edit', [
            'newsPost' => $newsPost,
            'categories' => NewsPost::CATEGORIES,
        ]);
    }

    public function update(StaffNewsPostRequest $request, NewsPost $newsPost): RedirectResponse
    {
        $data = $this->postData($request);
        $oldImagePath = $newsPost->image_path;
        $oldDocumentPath = $newsPost->document_path;

        try {
            if ($request->hasFile('image')) {
                $data['image_path'] = $this->storeImage($request->file('image'));
                $data['image_asset'] = null;
            } elseif ($request->boolean('remove_image')) {
                $data['image_path'] = null;
                $data['image_asset'] = null;
                $data['image_alt'] = null;
            }

            if ($request->hasFile('document')) {
                $this->addDocumentData($data, $request->file('document'));
            } elseif ($request->boolean('remove_document')) {
                $data['document_path'] = null;
                $data['document_name'] = null;
                $data['document_label'] = null;
                $data['document_size'] = null;
            }

            $newsPost->update($data);
        } catch (\Throwable $exception) {
            if (isset($data['image_path']) && $data['image_path'] !== $oldImagePath) {
                Storage::disk('public')->delete($data['image_path']);
            }

            if (isset($data['document_path']) && $data['document_path'] !== $oldDocumentPath) {
                Storage::disk('local')->delete($data['document_path']);
            }

            throw $exception;
        }

        if ($oldImagePath && $oldImagePath !== $newsPost->image_path) {
            Storage::disk('public')->delete($oldImagePath);
        }

        if ($oldDocumentPath && $oldDocumentPath !== $newsPost->document_path) {
            Storage::disk('local')->delete($oldDocumentPath);
        }

        return redirect()
            ->route('staff.news.edit', $newsPost)
            ->with('status', 'The news update was saved.');
    }

    public function preview(NewsPost $newsPost): View
    {
        return view('staff.news.preview', ['newsPost' => $newsPost]);
    }

    public function document(NewsPost $newsPost): StreamedResponse
    {
        abort_unless(
            $newsPost->document_path && Storage::disk('local')->exists($newsPost->document_path),
            404,
        );

        return Storage::disk('local')->download(
            $newsPost->document_path,
            $newsPost->document_name ?: Str::slug($newsPost->title).'.pdf',
            ['Content-Type' => 'application/pdf'],
        );
    }

    public function destroy(NewsPost $newsPost): RedirectResponse
    {
        $newsPost->delete();

        return redirect()
            ->route('staff.news.index')
            ->with('status', 'The news update was removed from the website.');
    }

    private function postData(StaffNewsPostRequest $request): array
    {
        $data = Arr::except($request->validated(), [
            'image',
            'remove_image',
            'document',
            'remove_document',
        ]);
        $data['external_url'] = ($data['external_url'] ?? null) ?: null;
        $data['body'] = ($data['body'] ?? null) ?: null;
        $data['image_alt'] = ($data['image_alt'] ?? null) ?: null;

        if ($data['status'] === NewsPost::STATUS_PUBLISHED && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        return $data;
    }

    private function uniqueSlug(string $title): string
    {
        $baseSlug = Str::slug($title) ?: 'news-update';
        $slug = $baseSlug;
        $suffix = 2;

        while (NewsPost::withTrashed()->where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    private function storeImage(UploadedFile $image): string
    {
        $path = $image->store('news', 'public');

        if (! is_string($path) || $path === '') {
            throw new RuntimeException('The news image could not be stored.');
        }

        return $path;
    }

    private function addDocumentData(array &$data, UploadedFile $document): void
    {
        $path = $document->store('news-documents', 'local');

        if (! is_string($path) || $path === '') {
            throw new RuntimeException('The public document could not be stored.');
        }

        $originalName = basename(str_replace('\\', '/', $document->getClientOriginalName()));
        $safeName = preg_replace('/[^\pL\pN ._()-]/u', '', $originalName) ?: 'public-document.pdf';

        $safeBaseName = pathinfo($safeName, PATHINFO_FILENAME) ?: 'public-document';

        $data['document_path'] = $path;
        $data['document_name'] = Str::limit($safeBaseName, 190, '').'.pdf';
        $data['document_size'] = $document->getSize();
    }
}

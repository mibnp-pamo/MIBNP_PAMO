@if ($errors->any())
    <div class="staff-alert staff-alert-error staff-editor-wide" role="alert">
        <strong>Please correct the following:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<section class="staff-editor-panel">
    <div class="staff-panel-heading">
        <p class="staff-eyebrow">Public content</p>
        <h2>Update details</h2>
    </div>

    <label class="staff-field">
        <span>Title <em>Required</em></span>
        <input type="text" name="title" value="{{ old('title', $newsPost->title) }}" maxlength="160" required>
    </label>

    <label class="staff-field">
        <span>Short summary <em>Required</em></span>
        <textarea name="summary" rows="4" maxlength="500" required>{{ old('summary', $newsPost->summary) }}</textarea>
        <small>Shown in the News Corner and used as the page description.</small>
    </label>

    <label class="staff-field">
        <span>Full update</span>
        <textarea name="body" rows="12">{{ old('body', $newsPost->body) }}</textarea>
        <small>Required when no external source URL is supplied. Plain text and paragraph breaks are supported.</small>
    </label>

    <div class="staff-field-grid">
        <label class="staff-field">
            <span>Category <em>Required</em></span>
            <select name="category" required>
                <option value="">Choose a category</option>
                @foreach ($categories as $category)
                    <option value="{{ $category }}" @selected(old('category', $newsPost->category) === $category)>
                        {{ $category }}
                    </option>
                @endforeach
            </select>
        </label>

        <label class="staff-field">
            <span>Source <em>Required</em></span>
            <input type="text" name="source" value="{{ old('source', $newsPost->source) }}" maxlength="120" required>
        </label>
    </div>

    <label class="staff-field">
        <span>External source URL</span>
        <input
            type="url"
            name="external_url"
            value="{{ old('external_url', $newsPost->external_url) }}"
            maxlength="2048"
            placeholder="https://example.gov.ph/update"
        >
        <small>Leave empty to publish the full update as a page on this website.</small>
    </label>
</section>

<aside class="staff-editor-sidebar">
    <section class="staff-editor-panel">
        <div class="staff-panel-heading">
            <p class="staff-eyebrow">Visibility</p>
            <h2>Publishing</h2>
        </div>

        <label class="staff-field">
            <span>Status</span>
            <select name="status" required>
                <option value="draft" @selected(old('status', $newsPost->status) === 'draft')>Draft</option>
                <option value="published" @selected(old('status', $newsPost->status) === 'published')>Published</option>
            </select>
        </label>

        <label class="staff-field">
            <span>Publish date and time</span>
            <input
                type="datetime-local"
                name="published_at"
                value="{{ old('published_at', $newsPost->published_at?->format('Y-m-d\TH:i')) }}"
            >
            <small>A future time schedules the update.</small>
        </label>

        <label class="staff-field">
            <span>Expiration date and time</span>
            <input
                type="datetime-local"
                name="expires_at"
                value="{{ old('expires_at', $newsPost->expires_at?->format('Y-m-d\TH:i')) }}"
            >
            <small>Optional. The update is hidden automatically afterward.</small>
        </label>

        <label class="staff-check staff-check-card">
            <input type="checkbox" name="is_pinned" value="1" @checked(old('is_pinned', $newsPost->is_pinned))>
            <span>
                <strong>Feature this update</strong>
                <small>Featured updates are displayed before newer regular posts.</small>
            </span>
        </label>
    </section>

    <section class="staff-editor-panel">
        <div class="staff-panel-heading">
            <p class="staff-eyebrow">Visitor resource</p>
            <h2>PDF document</h2>
        </div>

        @if ($newsPost->exists && $newsPost->document_path)
            <div class="staff-document-card">
                <div>
                    <strong>{{ $newsPost->documentDisplayName() }}</strong>
                    <span>PDF{{ $newsPost->documentSizeLabel() ? ' · '.$newsPost->documentSizeLabel() : '' }}</span>
                </div>
                <a href="{{ route('staff.news.document', $newsPost) }}">Download</a>
            </div>
        @endif

        <label class="staff-field">
            <span>{{ $newsPost->document_path ? 'Replace PDF' : 'Upload PDF' }}</span>
            <input type="file" name="document" accept="application/pdf,.pdf">
            <small>PDF only; maximum 10 MB. Files containing scripts, embedded files, or launch actions are rejected.</small>
        </label>

        <label class="staff-field">
            <span>Visitor-facing document label</span>
            <input
                type="text"
                name="document_label"
                value="{{ old('document_label', $newsPost->document_label) }}"
                maxlength="160"
                placeholder="Example: 2026 Visitor Guidelines"
            >
            <small>Optional. The uploaded filename is shown when this is empty.</small>
        </label>

        @if ($newsPost->exists && $newsPost->document_path)
            <label class="staff-check">
                <input type="checkbox" name="remove_document" value="1">
                <span>Remove the current PDF</span>
            </label>
        @endif
    </section>

    <section class="staff-editor-panel">
        <div class="staff-panel-heading">
            <p class="staff-eyebrow">Optional</p>
            <h2>Featured image</h2>
        </div>

        @if ($newsPost->exists && ($newsPost->image_path || $newsPost->image_asset))
            <img class="staff-current-image" src="{{ $newsPost->publicImage() }}" alt="{{ $newsPost->image_alt }}">
        @endif

        <label class="staff-field">
            <span>Upload image</span>
            <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
            <small>JPEG, PNG, or WebP; maximum 5 MB.</small>
        </label>

        <label class="staff-field">
            <span>Image description</span>
            <input type="text" name="image_alt" value="{{ old('image_alt', $newsPost->image_alt) }}" maxlength="255">
            <small>Describe the meaningful visual content for visitors using screen readers.</small>
        </label>

        @if ($newsPost->exists && ($newsPost->image_path || $newsPost->image_asset))
            <label class="staff-check">
                <input type="checkbox" name="remove_image" value="1">
                <span>Remove the current image</span>
            </label>
        @endif
    </section>

    <div class="staff-save-bar">
        <button class="staff-button staff-button-primary staff-button-full" type="submit">
            {{ $newsPost->exists ? 'Save changes' : 'Create update' }}
        </button>
    </div>
</aside>

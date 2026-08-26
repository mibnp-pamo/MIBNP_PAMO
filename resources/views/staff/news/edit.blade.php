@extends('layouts.staff')

@section('title', 'Edit news update')

@section('content')
    <div class="staff-page-heading">
        <div>
            <p class="staff-eyebrow">News editor</p>
            <h1>Edit update</h1>
            <p class="staff-muted">Changes become public immediately when this item is published and within its display dates.</p>
        </div>
        <div class="staff-heading-actions">
            <a class="staff-button staff-button-secondary" href="{{ route('staff.news.preview', $newsPost) }}">Preview</a>
            <a class="staff-button staff-button-secondary" href="{{ route('staff.news.index') }}">Back</a>
        </div>
    </div>

    <form class="staff-editor" method="POST" action="{{ route('staff.news.update', $newsPost) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('staff.news.partials.form')
    </form>

    <details class="staff-danger-zone">
        <summary>Remove this update</summary>
        <div>
            <p>This hides the update from the website. Its database record is retained for recovery.</p>
            <form method="POST" action="{{ route('staff.news.destroy', $newsPost) }}">
                @csrf
                @method('DELETE')
                <button class="staff-button staff-button-danger" type="submit">Remove update</button>
            </form>
        </div>
    </details>
@endsection

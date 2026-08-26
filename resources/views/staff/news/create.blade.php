@extends('layouts.staff')

@section('title', 'Create news update')

@section('content')
    <div class="staff-page-heading">
        <div>
            <p class="staff-eyebrow">News editor</p>
            <h1>Create update</h1>
            <p class="staff-muted">Save it as a draft until it is ready for public viewing.</p>
        </div>
        <a class="staff-button staff-button-secondary" href="{{ route('staff.news.index') }}">Back to updates</a>
    </div>

    <form class="staff-editor" method="POST" action="{{ route('staff.news.store') }}" enctype="multipart/form-data">
        @csrf
        @include('staff.news.partials.form')
    </form>
@endsection

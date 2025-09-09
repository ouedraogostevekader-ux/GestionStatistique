@extends('layouts.admin')
@section('breadcrumb', 'Administration/Logs')
@push('page_css')
    @livewireStyles
@endpush
@section('content')
    <section class="content-header">
        <h2 class="">
            Logs <span class="badge badge-success">{{ $logs->count() }}</span>
        </h2>
        <br>

    </section>
    <div class="content">
        <div class="clearfix"></div>

        <div class="clearfix"></div>
        <livewire:logs-search />
    </div>
@endsection
@push('page_scripts')
    @livewireScripts
    <script type="text/javascript"></script>
@endpush

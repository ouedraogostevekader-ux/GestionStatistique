@extends('layouts.admin')
@section('breadcrumb', 'logs')
@push('page_css')
    @livewireStyles
@endpush
@section('content')
    <section class="content-header">
        <div class="container-fluid col-sm-12">
            <h3 class="breadcrumb-item active">Liste des logs <span class="btn btn-success">({{ $logs->count() }})</span>
            </h3>
        </div>
    </section>
    <div class="content px-3">
        <div class="clearfix"></div>

        <div class="clearfix"></div>
        <livewire:logs-search />
    </div>
@endsection
@push('page_scripts')
    @livewireScripts
    <script type="text/javascript"></script>
@endpush

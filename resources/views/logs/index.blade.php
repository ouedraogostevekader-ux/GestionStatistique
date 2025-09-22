@extends('layouts.admin')
@section('breadcrumb', 'Administration/Logs')
@push('page_css')
    @livewireStyles
@endpush
@section('content')
    <section class="content-header">
        <div class="container row">
            <div class="col-sm-6">
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item active">Listes des logs <span
                            class="btn btn-info">({{ $logs->count() }})</span>
                    </li>
                </ol>
            </div>
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

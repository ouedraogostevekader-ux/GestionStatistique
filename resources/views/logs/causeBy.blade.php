@extends('layouts.admin')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-4">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item active">Logs avec declensseur : <span
                                class="badge bg-success">{{ $logs->count() }}</span></li>
                    </ol>
                </div>
                <div class="col-sm-8">
                    <div class="mt-4 d-flex justify-content-end">
                        <a class="btn btn-secondary" href="javascript:history.back()">
                            <i class="bi bi-arrow-left"></i> Retour
                        </a>
                    </div>

                </div>
            </div>
        </div>

    </section>
    <div class="content px-3">
        <div class="clearfix"></div>

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="table-responsive">
                @include('logs.tableC')
            </div>
        </div>
    </div>
@endsection

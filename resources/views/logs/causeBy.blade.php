@extends('layouts.guest')

@section('content')
    <section class="content-header">
        <h2 class="">
            Logs
        </h2>
        <br>

    </section>
    <div class="content">
        <div class="clearfix"></div>

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="table-responsive">
                @include('admin.logs.tableC')
            </div>
        </div>
    </div>
@endsection

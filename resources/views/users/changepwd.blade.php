@extends('layouts.admin')

@section('content')
<div class="panel-pane container-fluid main">
    <div class="container">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1>
                        Modifier votre mot de passe
                    </h1>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">


        <div class="card">
            @if($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                <ul>
                    <li>{{$error}}</li>
                </ul>
                @endforeach
            </div>
            @endif
            <form method="POST" action="{{ route('users.changepwdstore', $user->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="card-body">
                    <div class="row">
                        <!-- Password Field -->
                        <div class="form-group col-sm-6">
                            <label for="password">Nouveau mot de passe</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>

                        <div class="form-group col-sm-6">
                            <label for="confirm-password">Confirmer nouveau le mot de passe</label>
                            <input type="password" name="confirm-password" id="confirm-password"
                                   class="form-control" placeholder="Confirmez">
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('/') }}" class="btn btn-default">Annuler</a>
                </div>
            </form>

        </div>
    </div>
    </div>
</div>
@endsection

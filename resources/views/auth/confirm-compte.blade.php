<x-guest-layout>
    <!-- Latest compiled and minified CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <div class="w-full max-w-md bg-white rounded-lg shadow-lg p-6">
        <div class="card">
            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <ul>
                            <li>{{ $error }}</li>
                        </ul>
                    @endforeach
                </div>
            @endif
            <form method="POST" action="{{ route('update.compte', $user->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="card-body">
                    <div class="row">
                        <!-- Champ hidden qui prend nom + prénom -->
                        <input type="hidden" name="name" id="name">

                        <!-- Name Field -->
                        <div class="col-sm-12">
                            <label for="nom" class="form-label fw-bold">Nom</label>
                            <input type="text" name="nom" id="nom"
                                class="form-control @error('nom') is-invalid @enderror" maxlength="255"
                                value="{{ old('nom', $user->nom ?? null) }}" required>
                            @error('nom')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-12">
                            <label for="prenom" class="form-label fw-bold">Prénom</label>
                            <input type="text" name="prenom" id="prenom"
                                class="form-control @error('prenom') is-invalid @enderror" maxlength="255"
                                value="{{ old('prenom', $user->prenom ?? null) }}" required>
                            @error('prenom')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-sm-12">
                            <label for="matricule" class="form-label fw-bold">Matricule</label>
                            <input type="text" name="matricule" id="matricule"
                                class="form-control @error('matricule') is-invalid @enderror" maxlength="255"
                                value="{{ old('matricule', $user->matricule ?? null) }}" required>
                            @error('matricule')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-12">
                            <label for="fonction" class="form-label fw-bold">Fonction</label>
                            <input type="text" name="fonction" id="fonction"
                                class="form-control @error('fonction') is-invalid @enderror" maxlength="255"
                                value="{{ old('fonction', $user->fonction ?? null) }}" required>
                            @error('fonction')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-12">
                            <label for="telephone" class="form-label fw-bold">Telephone</label>
                            <input type="text" name="telephone" id="telephone"
                                class="form-control @error('telephone') is-invalid @enderror" maxlength="255"
                                value="{{ old('telephone', $user->telephone ?? null) }}" required>
                            @error('telephone')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-12">
                            <label for="structure_id" class="form-label fw-bold">Structure</label>
                            <select class="form-select @error('title') is-invalid @enderror" name="structure_id"
                                data-placeholder="Selectionner" style="width: 100%;">
                                <option value="">--Veuillez choisir votre structure--</option>
                                @foreach ($structures as $struct)
                                    <option class="select2-selection__choice" value="{{ $struct->id }}"
                                        @if (isset($user) && $user->structure_id == $struct->id) selected @endif>
                                        {{ $struct->sigle }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Email Field -->
                        <div class="col-sm-12">
                            <label for="email" class="form-label fw-bold">Email</label>
                            <input type="email" name="email" id="email"
                                class="form-control @error('email') is-invalid @enderror" required maxlength="255"
                                value="{{ old('email', $user->email ?? null) }}">
                            @error('email')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password Field -->
                        <div class="col-sm-12">
                            <label for="password" class="form-label fw-bold">Mot de passe</label>
                            <div class="relative">
                                <input type="password" id="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror" maxlength="255">
                                <button type="button" onclick="togglePassword('password', this)"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500">
                                    <i class="bi bi-eye-fill"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirm Password Field -->
                        <div class="col-sm-12">
                            <label for="confirm-password" class="form-label fw-bold">Ressaisissez le mot de
                                passe</label>
                            <div class="relative">
                                <input type="password" id="confirm-password" name="confirm-password"
                                    class="form-control @error('confirm-password') is-invalid @enderror"
                                    placeholder="Confirmez">
                                <button type="button" onclick="togglePassword('confirm-password', this)"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500">
                                    <i class="bi bi-eye-fill"></i>
                                </button>
                            </div>
                            @error('confirm-password')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end">
                    <a href="{{ route('users.index') }}" class="btn btn-default">Annuler</a>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>

        </div>
    </div>
</x-guest-layout>

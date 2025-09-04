<!-- Champ hidden qui prend nom + prénom -->
<input type="hidden" name="name" id="name">

<!-- Name Field -->
<div class="form-group col-sm-6">
    <label for="nom" class="form-label">Nom</label>
    <input type="text" name="nom" id="nom" class="form-control @error('nom') is-invalid @enderror"
        maxlength="255" value="{{ old('nom', $user->nom ?? null) }}" required>
    @error('nom')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror
</div>
<div class="form-group col-sm-6">
    <label for="prenom" class="form-label">Prénom</label>
    <input type="text" name="prenom" id="prenom" class="form-control @error('prenom') is-invalid @enderror"
        maxlength="255" value="{{ old('prenom', $user->prenom ?? null) }}" required>
    @error('prenom')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror
</div>

<div class="form-group col-sm-6">
    <label for="matricule" class="form-label">Matricule</label>
    <input type="text" name="matricule" id="matricule" class="form-control @error('matricule') is-invalid @enderror"
        maxlength="255" value="{{ old('matricule', $user->matricule ?? null) }}" required>
    @error('matricule')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror
</div>
<div class="form-group col-sm-6">
    <label for="fonction" class="form-label">Fonction</label>
    <input type="text" name="fonction" id="fonction" class="form-control @error('fonction') is-invalid @enderror"
        maxlength="255" value="{{ old('fonction', $user->fonction ?? null) }}" required>
    @error('fonction')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror
</div>
<div class="form-group col-sm-6">
    <label for="telephone" class="form-label">Telephone</label>
    <input type="text" name="telephone" id="telephone" class="form-control @error('telephone') is-invalid @enderror"
        maxlength="255" value="{{ old('telephone', $user->telephone ?? null) }}" required>
    @error('telephone')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror
</div>
<div class="form-group col-sm-6">
    <label for="structure_id" class="form-label">Structure</label>
    <select class="form-select @error('title') is-invalid @enderror" name="structure_id" data-placeholder="Selectionner"
        style="width: 100%;">
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
<div class="form-group col-sm-6">
    <label for="email" class="form-label">Email</label>
    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
        required maxlength="255" value="{{ old('email', $user->email ?? null) }}">
    @error('email')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror
</div>

{{-- <!-- Password Field -->
<div class="form-group col-sm-6">
    <label for="password" class="form-label">Mot de passe</label>
    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror"
        maxlength="255">
    @error('password')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror
</div>

<div class="form-group col-sm-6">
    <label for="confirm-password" class="form-label">Ressaisissez le mot de passe</label>
    <input type="password" name="confirm-password" id="confirm-password"
        class="form-control @error('confirm-password') is-invalid @enderror" placeholder="Confirmez">
    @error('confirm-password')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror
</div> --}}

<!-- Roles Field -->
<div class="form-group col-sm-6">
    <label class="form-label">Role:</label>
    <select class="form-select select2" name="roles[]" multiple="multiple" data-placeholder="Selectionner"
        style="width: 100%;">
        @foreach ($roles as $role)
            <option class="select2-selection__choice" value="{{ $role->name }}"
                @if (isset($user) && $user->roles->containsStrict('name', $role->name)) selected @endif>
                {{ $role->name }}
            </option>
        @endforeach
    </select>
</div>
{{--
<div class="form-group col-sm-6">

    <div class="custom-file">
        <label class="form-label">Photo de profil</label>
        <input type="file" class="form-control @error('photo') is-invalid @enderror" name="photo" accept="image/*">
        @error('photo')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    <small class="form-text text-muted">
        Formats acceptés: JPG, PNG, GIF. Taille max: 2MB.
    </small>
    @if (isset($user) && $user->photo != null)
        <div class="avatar-preview">
            <img src="{{ asset('storage/' . $user->photo) }}" alt="PHOTO PROFIL">
        </div>
    @endif
</div> --}}


@push('page_scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"
        integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"
        integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{ asset('dist/js/select2.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const nom = document.getElementById("nom");
            const prenom = document.getElementById("prenom");
            const Name = document.getElementById("name");

            function updateName() {
                Name.value = nom.value.trim() + " " + prenom.value.trim();
            }

            nom.addEventListener("input", updateName);
            prenom.addEventListener("input", updateName);

            // Initialiser si déjà rempli
            updateName();
        });
    </script>
@endpush

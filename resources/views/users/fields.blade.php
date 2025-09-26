<!-- Champ hidden qui prend nom + prénom -->
<input type="hidden" name="name" id="name">

<!-- Name Field -->
<div class="form-group col-sm-6">
    <label for="nom" class="form-label fw-bold">Nom<span id="colorant">*</span></label>
    <input type="text" name="nom" id="nom" class="form-control @error('nom') is-invalid @enderror"
        maxlength="255" value="{{ old('nom', $user->nom ?? null) }}" required>
    @error('nom')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror
</div>
<div class="form-group col-sm-6">
    <label for="prenom" class="form-label fw-bold">Prénom<span id="colorant">*</span></label>
    <input type="text" name="prenom" id="prenom" class="form-control @error('prenom') is-invalid @enderror"
        maxlength="255" value="{{ old('prenom', $user->prenom ?? null) }}" required>
    @error('prenom')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror
</div>

<div class="form-group col-sm-6">
    <label for="matricule" class="form-label fw-bold">Matricule<span id="colorant">*</span></label>
    <input type="text" name="matricule" id="matricule" class="form-control @error('matricule') is-invalid @enderror"
        maxlength="255" value="{{ old('matricule', $user->matricule ?? null) }}" required>
    @error('matricule')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror
</div>
<div class="form-group col-sm-6">
    <label for="fonction" class="form-label fw-bold">Fonction<span id="colorant">*</span></label>
    <input type="text" name="fonction" id="fonction" class="form-control @error('fonction') is-invalid @enderror"
        maxlength="255" value="{{ old('fonction', $user->fonction ?? null) }}" required>
    @error('fonction')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror
</div>
<div class="form-group col-sm-6">
    <label for="telephone" class="form-label fw-bold">Telephone<span id="colorant">*</span></label>
    <input type="text" name="telephone" id="telephone" class="form-control @error('telephone') is-invalid @enderror"
        maxlength="8" minlength="8" value="{{ old('telephone', $user->telephone ?? null) }}" required>
    @error('telephone')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror
</div>
<div class="form-group col-sm-6">
    <label for="structure_id" class="form-label fw-bold">Structure<span id="colorant">*</span></label>
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
    <label for="email" class="form-label fw-bold">Email<span id="colorant">*</span></label>
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
    <label class="form-label fw-bold">Role<span id="colorant">*</span>:</label>
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

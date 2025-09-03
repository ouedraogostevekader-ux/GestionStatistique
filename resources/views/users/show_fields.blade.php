<!-- Name Field -->
<div class="col-sm-6">
    <label class="form-label">Nom & prénom</label>:
    {{ $user->name }}
</div>

<!-- Email Field -->
<div class="col-sm-6">
    <label class="form-label">Email</label>:
    {{ $user->email }}
</div>
<!-- Email Field -->
<div class="col-sm-6">
    <label class="form-label">Téléphone</label>:
    {{ $user->telephone }}
</div>

<div class="col-sm-6">
    <label class="form-label">Matricule</label>:
    {{ $user->matricule }}
</div>

<div class="col-sm-6">
    <label class="form-label">Fonction</label>:
    {{ $user->fonction }}
</div>

<div class="col-sm-6">
    <label class="form-label">Structure</label>:
    {{ $user->structure->sigle }} : {{ $user->structure->nom }}
</div>

<div class="form-group col-sm-12">
    <label class="form-label">Role</label>: |
    @foreach ($user->roles as $role)
        {{ $role->name }} |
    @endforeach
</div>

<div class="col-sm-6">
    <label class="form-label">Photo de profile</label>:
    <div class="avatar-preview">
        @if ($user->photo != null)
            <img src="{{ asset('storage/' . $user->photo) }}" alt="PHOTO PROFIL">
        @else
            Pas de photo
        @endif
    </div>
</div>

@extends('layouts.app')

@section('title', 'Modifier utilisateur')

@section('content')
<h1>Modifier : {{ $user->name }}</h1>

<form action="{{ route('admin.users.update', $user) }}" method="POST">
    @csrf @method('PUT')
    <div class="mb-3">
        <label for="name" class="form-label">Nom</label>
        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
    </div>
    <div class="mb-3">
        <label for="role" class="form-label">Rôle</label>
        <select class="form-select" id="role" name="role">
            <option value="student" {{ $user->role == 'student' ? 'selected' : '' }}>Étudiant</option>
            <option value="instructor" {{ $user->role == 'instructor' ? 'selected' : '' }}>Instructeur</option>
            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Administrateur</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="bio" class="form-label">Bio</label>
        <textarea class="form-control" id="bio" name="bio" rows="3">{{ old('bio', $user->bio) }}</textarea>
    </div>
    <button type="submit" class="btn btn-primary">Mettre à jour</button>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Annuler</a>
</form>
@endsection
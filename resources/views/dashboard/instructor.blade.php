@extends('layouts.app')

@section('title', 'Espace instructeur')

@section('content')
<h1>Espace instructeur</h1>

<div class="row mt-4">
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">Mes cours</h5>
                <p class="display-6">{{ $courses->count() }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">Cours publiés</h5>
                <p class="display-6">{{ $publishedCourses }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5 class="card-title">Étudiants inscrits</h5>
                <p class="display-6">{{ $totalStudents }}</p>
            </div>
        </div>
    </div>
</div>

<a href="{{ route('instructor.courses.create') }}" class="btn btn-primary mb-3">Créer un nouveau cours</a>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Titre</th>
            <th>Étudiants</th>
            <th>Statut</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($courses as $course)
        <tr>
            <td>{{ $course->title }}</td>
            <td>{{ $course->enrollments_count }}</td>
            <td>{{ $course->published ? 'Publié' : 'Brouillon' }}</td>
            <td>
                <a href="{{ route('instructor.courses.edit', $course) }}" class="btn btn-sm btn-warning">Modifier</a>
                <form action="{{ route('instructor.courses.destroy', $course) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ce cours ?')">Supprimer</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="4">Aucun cours créé.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
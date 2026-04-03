@extends('layouts.app')

@section('title', 'Administration')

@section('content')
<h1>Tableau de bord administrateur</h1>

<div class="row">
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body">
                <h5 class="card-title">Utilisateurs</h5>
                <p class="card-text display-6">{{ $totalUsers }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <h5 class="card-title">Cours</h5>
                <p class="card-text display-6">{{ $totalCourses }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info mb-3">
            <div class="card-body">
                <h5 class="card-title">Inscriptions</h5>
                <p class="card-text display-6">{{ $totalEnrollments }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning mb-3">
            <div class="card-body">
                <h5 class="card-title">Revenus</h5>
                <p class="card-text display-6">{{ number_format($totalRevenue, 0, ',', ' ') }} FCFA</p>
            </div>
        </div>
    </div>
</div>

<a href="{{ route('admin.users.index') }}" class="btn btn-primary">Gérer les utilisateurs</a>
@endsection
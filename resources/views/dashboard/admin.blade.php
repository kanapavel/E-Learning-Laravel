@extends('layouts.app')

@section('title', 'Administration')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">

    <!-- En-tête -->
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-display font-bold tracking-tight">Administration</h1>
        <p class="text-sm text-on-surface-variant mt-1">Gérez les utilisateurs, les cours et surveillez les statistiques globales.</p>
    </div>

    <!-- Cartes statistiques -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Utilisateurs -->
        <div class="bg-white rounded-2xl shadow-sm border border-outline/20 p-5 flex items-center justify-between hover:shadow-md transition">
            <div>
                <p class="text-xs font-medium text-on-surface-variant uppercase tracking-wide">Utilisateurs</p>
                <p class="text-3xl font-bold text-primary mt-1">{{ $totalUsers }}</p>
                <p class="text-xs text-primary/70 mt-1">+12% vs mois dernier</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center">
                <i class="fas fa-users text-primary text-xl"></i>
            </div>
        </div>

        <!-- Cours -->
        <div class="bg-white rounded-2xl shadow-sm border border-outline/20 p-5 flex items-center justify-between hover:shadow-md transition">
            <div>
                <p class="text-xs font-medium text-on-surface-variant uppercase tracking-wide">Cours</p>
                <p class="text-3xl font-bold text-blue-600 mt-1">{{ $totalCourses }}</p>
                <p class="text-xs text-blue-600/70 mt-1">{{ $publishedCourses ?? 0 }} publiés</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                <i class="fas fa-book-open text-blue-600 text-xl"></i>
            </div>
        </div>

        <!-- Inscriptions -->
        <div class="bg-white rounded-2xl shadow-sm border border-outline/20 p-5 flex items-center justify-between hover:shadow-md transition">
            <div>
                <p class="text-xs font-medium text-on-surface-variant uppercase tracking-wide">Inscriptions</p>
                <p class="text-3xl font-bold text-green-600 mt-1">{{ $totalEnrollments }}</p>
                <p class="text-xs text-green-600/70 mt-1">+8% ce mois</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                <i class="fas fa-user-graduate text-green-600 text-xl"></i>
            </div>
        </div>

        <!-- Revenus -->
        <div class="bg-white rounded-2xl shadow-sm border border-outline/20 p-5 flex items-center justify-between hover:shadow-md transition">
            <div>
                <p class="text-xs font-medium text-on-surface-variant uppercase tracking-wide">Revenus</p>
                <p class="text-2xl font-bold text-purple-600 mt-1">{{ number_format($totalRevenue, 0, ',', ' ') }} FCFA</p>
                <p class="text-xs text-purple-600/70 mt-1">+18% vs trimestre précédent</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
                <i class="fas fa-money-bill-wave text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Gestion des utilisateurs -->
        <div class="bg-white rounded-2xl border border-outline/20 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-outline/20 bg-surface-low/50">
                <h2 class="text-md font-display font-semibold flex items-center gap-2">
                    <i class="fas fa-users text-primary"></i> Utilisateurs
                </h2>
            </div>
            <div class="p-5 space-y-3">
                <p class="text-sm text-on-surface-variant">Gérez les comptes, rôles et permissions des utilisateurs.</p>
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 text-primary hover:underline text-sm font-medium">
                    <i class="fas fa-arrow-right"></i> Gérer les utilisateurs
                </a>
            </div>
        </div>

        <!-- Rapports (exemple) -->
        <div class="bg-white rounded-2xl border border-outline/20 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-outline/20 bg-surface-low/50">
                <h2 class="text-md font-display font-semibold flex items-center gap-2">
                    <i class="fas fa-chart-line text-primary"></i> Rapports
                </h2>
            </div>
            <div class="p-5 space-y-3">
                <p class="text-sm text-on-surface-variant">Téléchargez les rapports d’activité et les analyses.</p>
                <a href="#" class="inline-flex items-center gap-2 text-primary hover:underline text-sm font-medium">
                    <i class="fas fa-download"></i> Télécharger le rapport
                </a>
            </div>
        </div>
    </div>

    <!-- Dernières activités (exemple) -->
    <div class="bg-white rounded-2xl border border-outline/20 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-outline/20 bg-surface-low/50">
            <h2 class="text-md font-display font-semibold flex items-center gap-2">
                <i class="fas fa-history text-primary"></i> Dernières activités
            </h2>
        </div>
        <div class="divide-y divide-outline/20">
            <div class="px-5 py-4 flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center">
                    <i class="fas fa-user-plus text-primary text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium">Nouvel utilisateur inscrit</p>
                    <p class="text-xs text-on-surface-variant">Il y a 2 heures</p>
                </div>
            </div>
            <div class="px-5 py-4 flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium">Cours « Laravel 11 » publié</p>
                    <p class="text-xs text-on-surface-variant">Il y a 5 heures</p>
                </div>
            </div>
            <div class="px-5 py-4 flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center">
                    <i class="fas fa-credit-card text-purple-600 text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium">Nouveau paiement reçu</p>
                    <p class="text-xs text-on-surface-variant">Il y a 1 jour</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
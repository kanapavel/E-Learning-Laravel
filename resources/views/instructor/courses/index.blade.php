@extends('layouts.app')

@section('title', 'Mes cours')

@section('content')
<div class="container mx-auto">
    <h1 class="text-2xl font-bold mb-4">Mes cours</h1>
    <a href="{{ route('instructor.courses.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">Nouveau cours</a>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($courses as $course)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <img src="{{ $course->thumbnail_url }}" class="w-full h-40 object-cover">
            <div class="p-4">
                <h5 class="font-bold text-lg">{{ $course->title }}</h5>
                <p class="text-gray-600 text-sm mt-1">{{ Str::limit($course->description, 80) }}</p>
                <div class="mt-2 flex justify-between items-center">
                    <span class="bg-gray-200 text-gray-800 text-xs px-2 py-1 rounded">{{ ucfirst($course->level) }}</span>
                    <div>
                        <a href="{{ route('instructor.courses.edit', $course) }}" class="text-indigo-600 hover:text-indigo-800 mr-2">Modifier</a>
                        <form action="{{ route('instructor.courses.destroy', $course) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Supprimer ce cours ?')">Supprimer</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    {{ $courses->links() }}
</div>
@endsection
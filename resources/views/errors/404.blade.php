@extends('layouts.filament-error')

@section('title', __('Página no encontrada'))

@section('content')
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-50 text-center px-6 py-12">
        <div class="flex flex-col items-center space-y-4">

            <svg class="w-20 h-20 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9.75 9.75h.008v.008H9.75V9.75zm4.5 0h.008v.008h-.008V9.75zm-6.364 6.364a6 6 0 118.485 0m-8.485 0a6 6 0 008.485 0M21 12A9 9 0 113 12a9 9 0 0118 0z"/>
            </svg>

            <h1 class="text-3xl font-bold text-gray-950">404</h1>

            <p class="text-lg text-gray-700">
                {{ __('Lo sentimos, la página que estás buscando no fue encontrada.') }}
            </p>

            <a href="{{ route('filament.admin.pages.dashboard') }}"
               class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-custom-500 text-white text-sm font-medium rounded-lg transition">
                {{ __('Ir al panel') }}
            </a>

        </div>
    </div>
@endsection

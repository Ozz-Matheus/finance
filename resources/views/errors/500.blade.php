@extends('layouts.filament-error')

@section('title', __('Error del servidor'))

@section('content')
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-50 text-center px-6 py-12">
        <div class="flex flex-col items-center space-y-4">

			<svg class="w-20 h-20 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
			  <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
			</svg>

			<h1 class="text-3xl font-bold text-gray-950">500</h1>

			<p class="text-lg text-gray-700">
			    {{ __('Algo salió mal. Por favor intenta más tarde.') }}
			</p>

            <a href="{{ route('filament.admin.pages.dashboard') }}"
               class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-custom-500 text-white text-sm font-medium rounded-lg transition">
                {{ __('Ir al panel') }}
            </a>

        </div>
    </div>
@endsection

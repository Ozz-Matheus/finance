@extends('layouts.filament-error')

@section('title', __('Página expirada'))

@section('content')
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-50 text-center px-6 py-12">
        <div class="flex flex-col items-center space-y-4">

			<svg class="w-20 h-20 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
			  <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
			</svg>



            <h1 class="text-3xl font-bold text-gray-950">404</h1>

            <p class="text-lg text-gray-700">
                {{ __('Lo sentimos, tu sesión ha expirado. Actualízala y vuelve a intentarlo.') }}
            </p>

            <a href="{{ route('filament.admin.pages.dashboard') }}"
               class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-custom-500 text-white text-sm font-medium rounded-lg transition">
                {{ __('Ir al panel') }}
            </a>

        </div>
    </div>
@endsection

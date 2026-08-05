<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\FormBuilder;
use App\Livewire\FormDashboard;
use App\Livewire\TemplateLibrary;
use App\Livewire\PublicFormRenderer;
use App\Models\Form;
use App\Models\FormVersion;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth'])->group(function () {
    Route::get('/forms/{form}/builder', FormBuilder::class)->name('builder');
    Route::get('/forms/{form}/dashboard', FormDashboard::class)->name('form.dashboard');
    Route::get('/templates', TemplateLibrary::class)->name('templates');
});

Route::get('/f/{formVersion}', PublicFormRenderer::class)->name('form.show');

require __DIR__.'/auth.php';

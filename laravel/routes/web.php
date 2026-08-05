<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\FormBuilder;
use App\Livewire\FormDashboard;
use App\Livewire\TemplateLibrary;
use App\Livewire\PublicFormRenderer;
use App\Models\Form;
use App\Models\FormVersion;

Route::view('/', 'welcome');

Route::get('dashboard', function () {
    $forms = auth()->user()->forms()->orderBy('updated_at', 'desc')->get();
    return view('dashboard', compact('forms'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::post('/forms', function () {
    $form = auth()->user()->forms()->create([
        'title' => 'Untitled Form',
        'slug' => \Illuminate\Support\Str::slug('Untitled Form ' . time()),
        'status' => 'draft',
    ]);
    
    // Create initial version
    $version = $form->versions()->create([
        'schema_data' => [
            'version' => '1.0.0',
            'metadata' => ['title' => 'Untitled Form'],
            'fields' => [],
            'layout' => ['sections' => [['id' => 'section_1', 'fields' => []]]]
        ],
        'schema_hash' => hash('sha256', json_encode([])),
        'created_by' => auth()->id(),
    ]);
    
    $form->update(['active_version_id' => $version->id]);
    
    return redirect()->route('builder', ['form' => $form->id]);
})->middleware(['auth'])->name('forms.store');

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

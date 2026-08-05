<!-- Locked height layout to prevent full page scrolling -->
<div class="h-screen bg-gray-50 dark:bg-gray-900 flex flex-col font-sans overflow-hidden" x-data="{ jsonView: @entangle('activeTab') }">
    <!-- AI Polling Overlay -->
    @if($hasPendingAiJob)
        <div wire:poll.2s="checkAiJobStatus" class="absolute inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-80 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-2xl text-center max-w-sm w-full mx-4 border border-purple-500/30">
                <div class="relative w-20 h-20 mx-auto mb-6">
                    <div class="absolute inset-0 rounded-full border-4 border-purple-500 opacity-20 animate-ping"></div>
                    <div class="absolute inset-0 rounded-full border-4 border-transparent border-t-purple-600 border-r-purple-400 animate-spin"></div>
                    <div class="absolute inset-0 flex items-center justify-center text-purple-500">
                        <svg class="w-8 h-8 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Generating Magic...</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ $aiJobStatus ?? 'AI is processing your request...' }}</p>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 mb-2 overflow-hidden">
                    <div class="bg-purple-600 h-1.5 rounded-full animate-pulse w-full"></div>
                </div>
                <p class="text-xs text-gray-400 dark:text-gray-500">This usually takes about 10-15 seconds depending on complexity.</p>
            </div>
        </div>
    @endif

    <!-- Top Header & Wizard Navigation -->
    <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 p-4 sticky top-0 z-10 shadow-sm">
        <div class="max-w-6xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 flex items-center">
                    <span class="mr-2">Form Builder</span>
                </h1>
                <div class="text-sm text-gray-500 dark:text-gray-400" wire:loading.class="animate-pulse">
                    {{ $saveStatus }}
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="relative">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                </div>
                <div class="relative flex justify-between">
                    @foreach(['Details', 'Builder', 'Settings', 'Finish'] as $index => $stepName)
                        @php $stepNum = $index + 1; @endphp
                        <div class="bg-white dark:bg-gray-800 px-4 flex items-center {{ $currentStep === $stepNum ? 'font-bold text-blue-600 dark:text-blue-400' : ($currentStep > $stepNum ? 'text-gray-900 dark:text-gray-300' : 'text-gray-400 dark:text-gray-500') }}">
                            <span class="h-6 w-6 rounded-full flex items-center justify-center text-xs text-white {{ $currentStep === $stepNum ? 'bg-blue-500 dark:bg-blue-600' : ($currentStep > $stepNum ? 'bg-gray-800 dark:bg-gray-500' : 'bg-gray-300 dark:bg-gray-700') }} mr-2">
                                @if($currentStep > $stepNum)
                                    ✓
                                @else
                                    {{ $stepNum }}
                                @endif
                            </span>
                            {{ $stepName }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 overflow-hidden w-full mx-auto p-4 md:p-8 max-w-6xl flex flex-col">
        
        <!-- STEP 1: Details -->
        @if($currentStep === 1)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-8 max-w-3xl mx-auto mt-8">
                <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-4 mb-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Form basics</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Enter the primary details for your new data-collection form.</p>
                    </div>
                    <span class="bg-blue-500 dark:bg-blue-600 text-white text-xs px-3 py-1 rounded">Survey form</span>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Form title <span class="text-red-500 dark:text-red-400">*</span>
                        </label>
                        <input type="text" wire:model.live.debounce.500ms="schema.metadata.title" 
                            class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400"
                            placeholder="e.g., Fall 2024 Registration">
                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-1 flex justify-between">
                            <span>{{ strlen($schema['metadata']['title'] ?? '') }}/200</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Description
                        </label>
                        <textarea wire:model.live.debounce.500ms="schema.metadata.description" rows="3"
                            class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400"
                            placeholder="Brief description of the form's purpose"></textarea>
                    </div>

                    <div class="pt-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center">
                            Public URL: <a href="{{ route('form.show', $form->active_version_id ?? 0) }}" class="text-blue-500 dark:text-blue-400 ml-2 hover:underline" target="_blank">{{ route('form.show', $form->active_version_id ?? 0) }}</a>
                        </p>
                    </div>
                </div>

                <div class="mt-8 flex justify-between items-center border-t border-gray-100 dark:border-gray-700 pt-6">
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 border border-red-200 dark:border-red-800 text-red-500 dark:text-red-400 rounded hover:bg-red-50 dark:hover:bg-red-900/30 transition text-sm">Cancel</a>
                    <button wire:click="nextStep" class="px-6 py-2 bg-blue-500 dark:bg-blue-600 text-white rounded hover:bg-blue-600 dark:hover:bg-blue-500 shadow-sm transition font-medium">Next: Builder &rarr;</button>
                </div>
            </div>
        @endif

        <!-- STEP 2: Builder -->
        @if($currentStep === 2)
            <div class="flex flex-col md:flex-row gap-6 h-full flex-1 min-h-0">
                
                <!-- Builder Canvas (Left) -->
                <div class="flex-1 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col">
                    <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex justify-between bg-gray-50 dark:bg-gray-700/50 rounded-t-lg">
                        <div class="flex space-x-2">
                            <button wire:click="undo" class="p-1.5 text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 rounded" title="Undo"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg></button>
                            <button wire:click="redo" class="p-1.5 text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 rounded" title="Redo"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10h-10a8 8 0 00-8 8v2M21 10l-6 6m6-6l-6-6"></path></svg></button>
                        </div>
                        <div class="flex space-x-2">
                            <input type="file" id="importFileInput" wire:model="importFile" class="hidden" accept=".docx,.xlsx">
                            <button onclick="document.getElementById('importFileInput').click()" class="px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-sm rounded shadow flex items-center space-x-1 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                <span>Import Word/Excel</span>
                            </button>
                        </div>
                    </div>
                    
                    <div class="p-8 flex-1 overflow-y-auto bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjEiIGZpbGw9IiNlN2U1ZTQiLz48L3N2Zz4=')] dark:bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjEiIGZpbGw9IiMzNzQxNTEiLz48L3N2Zz4=')]">
                        
                        @if(count($schema['fields']) === 0)
                            <div class="flex items-center justify-center h-full min-h-[300px] border-2 border-dashed border-blue-200 dark:border-blue-800 bg-blue-50/50 dark:bg-blue-900/10 rounded-lg text-blue-400 dark:text-blue-500">
                                Drag fields here or click in the sidebar to add.
                            </div>
                        @else
                            <div class="space-y-4 max-w-2xl mx-auto">
                                @foreach($schema['fields'] as $field)
                                    <div wire:key="{{ $field['id'] }}" 
                                         wire:click="selectField('{{ $field['id'] }}')" 
                                         draggable="true"
                                         wire:dragstart="dragStart('{{ $field['id'] }}')"
                                         wire:dragenter.prevent="dragEnter('{{ $field['id'] }}')"
                                         wire:dragover.prevent
                                         wire:drop="drop('{{ $field['id'] }}')"
                                         class="p-4 rounded-lg border {{ $selectedFieldId === $field['id'] ? 'border-blue-400 dark:border-blue-500 ring-1 ring-blue-400 dark:ring-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-blue-300 dark:hover:border-blue-500' }} group relative cursor-grab active:cursor-grabbing shadow-sm transition-all {{ $draggingFieldId === $field['id'] ? 'opacity-50 border-dashed border-2' : '' }}">
                                        
                                        <div class="flex justify-between items-center mb-2">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                                                <label class="font-medium text-gray-700 dark:text-gray-200 text-sm">
                                                    {{ $field['label'] ?? 'Untitled Field' }}
                                                    @if($field['required'] ?? false) <span class="text-red-500 dark:text-red-400">*</span> @endif
                                                </label>
                                            </div>
                                            
                                            <div class="hidden group-hover:flex space-x-1 text-gray-400 dark:text-gray-500">
                                                <button wire:click.stop="duplicateField('{{ $field['id'] }}')" class="p-1 hover:text-blue-500 dark:hover:text-blue-400" title="Duplicate"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg></button>
                                                <button wire:click.stop="deleteField('{{ $field['id'] }}')" class="p-1 hover:text-red-500 dark:hover:text-red-400" title="Delete"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                            </div>
                                        </div>
                                        
                                        @if($field['type'] === 'text' || $field['type'] === 'email' || $field['type'] === 'phone' || $field['type'] === 'number')
                                            <input type="text" disabled 
                                                   value="{{ $field['default'] ?? '' }}"
                                                   class="w-full border-gray-200 dark:border-gray-700 rounded bg-gray-50 dark:bg-gray-900 text-sm py-2 px-3 placeholder-gray-400 dark:placeholder-gray-600 text-gray-800 dark:text-gray-200 disabled:opacity-100" 
                                                   placeholder="{{ $field['placeholder'] ?? ucfirst($field['type']) . ' input placeholder' }}">
                                        @elseif($field['type'] === 'textarea')
                                            <textarea disabled class="w-full border-gray-200 dark:border-gray-700 rounded bg-gray-50 dark:bg-gray-900 text-sm py-2 px-3 placeholder-gray-400 dark:placeholder-gray-600 text-gray-800 dark:text-gray-200 disabled:opacity-100" rows="2" placeholder="{{ $field['placeholder'] ?? 'Long text...' }}">{{ $field['default'] ?? '' }}</textarea>
                                        @elseif($field['type'] === 'dropdown')
                                            <select disabled class="w-full border-gray-200 dark:border-gray-700 rounded bg-gray-50 dark:bg-gray-900 text-sm py-2 px-3 text-gray-500 dark:text-gray-400">
                                                <option>Select an option</option>
                                                @foreach($field['options'] ?? [] as $option)
                                                    <option>{{ $option['label'] ?? '' }}</option>
                                                @endforeach
                                            </select>
                                        @elseif($field['type'] === 'radio')
                                            <div class="space-y-2 mt-2">
                                                @foreach($field['options'] ?? [] as $option)
                                                    <label class="flex items-center">
                                                        <input type="radio" disabled class="text-blue-500 bg-gray-50 dark:bg-gray-900 border-gray-300 dark:border-gray-600">
                                                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ $option['label'] ?? '' }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @elseif($field['type'] === 'checkbox')
                                            <div class="space-y-2 mt-2">
                                                @foreach($field['options'] ?? [] as $option)
                                                    <label class="flex items-center">
                                                        <input type="checkbox" disabled class="rounded text-blue-500 bg-gray-50 dark:bg-gray-900 border-gray-300 dark:border-gray-600">
                                                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ $option['label'] ?? '' }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @elseif($field['type'] === 'section_heading')
                                            <div class="py-2 border-b border-gray-200 dark:border-gray-700">
                                                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $field['label'] ?? 'Section Heading' }}</h3>
                                                @if(!empty($field['help_text']))
                                                    <p class="text-sm text-gray-500 mt-1">{{ $field['help_text'] }}</p>
                                                @endif
                                            </div>
                                        @elseif($field['type'] === 'date')
                                            <input type="date" disabled 
                                                   class="w-full border-gray-200 dark:border-gray-700 rounded bg-gray-50 dark:bg-gray-900 text-sm py-2 px-3 text-gray-800 dark:text-gray-200 disabled:opacity-100">
                                        @elseif($field['type'] === 'file')
                                            <div class="w-full border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 flex flex-col items-center justify-center bg-gray-50 dark:bg-gray-900/50">
                                                <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                                <span class="text-sm text-gray-500 dark:text-gray-400">Click or drag file to upload</span>
                                            </div>
                                        @elseif($field['type'] === 'rating')
                                            <div class="flex items-center space-x-1 mt-1">
                                                @for($i = 1; $i <= ($field['validation']['max'] ?? 5); $i++)
                                                    <svg class="w-6 h-6 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                                @endfor
                                            </div>
                                        @else
                                            <div class="p-3 bg-gray-50 dark:bg-gray-900 rounded border border-gray-100 dark:border-gray-700 text-sm text-gray-400 dark:text-gray-500 text-center">
                                                [{{ ucfirst(str_replace('_', ' ', $field['type'])) }} Preview]
                                            </div>
                                        @endif
                                        
                                        @if(!empty($field['help_text']) && $field['type'] !== 'section_heading')
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $field['help_text'] }}</p>
                                        @endif
                                        
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="p-4 border-t border-gray-100 dark:border-gray-700 flex justify-between bg-white dark:bg-gray-800 rounded-b-lg">
                        <button wire:click="previousStep" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded hover:bg-gray-50 dark:hover:bg-gray-700 transition text-sm flex items-center">&larr; Back</button>
                        <button wire:click="nextStep" class="px-6 py-2 bg-blue-500 dark:bg-blue-600 text-white rounded hover:bg-blue-600 dark:hover:bg-blue-500 shadow-sm transition font-medium">Next: Settings &rarr;</button>
                    </div>
                </div>

                <!-- Fields Sidebar (Right) -->
                <div class="w-full md:w-80 flex flex-col">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 flex-1 overflow-hidden flex flex-col">
                        
                        <div class="flex border-b border-gray-200 dark:border-gray-700">
                            <button class="flex-1 py-3 text-sm font-medium border-b-2 {{ !$selectedFieldId ? 'border-blue-500 dark:border-blue-400 text-gray-900 dark:text-gray-100' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}" wire:click="$set('selectedFieldId', null)">
                                Add fields
                            </button>
                            <button class="flex-1 py-3 text-sm font-medium border-b-2 {{ $selectedFieldId ? 'border-blue-500 dark:border-blue-400 text-gray-900 dark:text-gray-100' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                Field options
                            </button>
                        </div>

                        <div class="p-4 overflow-y-auto flex-1">
                            @if(!$selectedFieldId)
                                <h3 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-4">Standard Fields</h3>
                                <div class="grid grid-cols-2 gap-3">
                                    @foreach(app(\App\Services\Builder\FieldRegistry::class)->getAllRegisteredTypes() as $type)
                                        <button wire:click="addField('{{ $type }}')" 
                                                class="flex flex-col items-center justify-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-900 hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:border-blue-300 dark:hover:border-blue-600 transition group">
                                            <svg class="w-6 h-6 text-gray-400 dark:text-gray-500 group-hover:text-blue-500 dark:group-hover:text-blue-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                                            <span class="text-xs font-medium text-gray-600 dark:text-gray-300 text-center">{{ ucfirst(str_replace('_', ' ', $type)) }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            @else
                                @php
                                    $selectedField = collect($schema['fields'])->firstWhere('id', $selectedFieldId);
                                @endphp
                                @if($selectedField)
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Field Label</label>
                                            <input type="text" 
                                                wire:model.live.debounce.500ms="editingFieldLabel"
                                                class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Variable Key</label>
                                            <input type="text" 
                                                wire:model.live.debounce.500ms="editingFieldKey"
                                                class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 font-mono">
                                        </div>
                                        
                                        <div class="pt-2 border-t border-gray-100 dark:border-gray-700">
                                            <label class="flex items-center">
                                                <input type="checkbox" 
                                                    wire:model.live="editingFieldRequired"
                                                    class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-blue-600 dark:text-blue-500 shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400">
                                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Required Field</span>
                                            </label>
                                        </div>

                                        <div class="pt-2 border-t border-gray-100 dark:border-gray-700">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Help Text</label>
                                            <input type="text" 
                                                wire:model.live.debounce.500ms="editingFieldHelpText"
                                                class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400"
                                                placeholder="Optional description">
                                        </div>

                                        @if(in_array($selectedField['type'], ['text', 'textarea', 'email', 'phone', 'number']))
                                            <div class="pt-2 border-t border-gray-100 dark:border-gray-700">
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Placeholder</label>
                                                <input type="text" 
                                                    wire:model.live.debounce.500ms="editingFieldPlaceholder"
                                                    class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400">
                                            </div>
                                        @endif

                                        @if(in_array($selectedField['type'], ['number', 'text', 'textarea']))
                                            <div class="pt-2 border-t border-gray-100 dark:border-gray-700 grid grid-cols-2 gap-2">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                        {{ $selectedField['type'] === 'number' ? 'Min' : 'Min Length' }}
                                                    </label>
                                                    <input type="number" 
                                                        wire:model.live.debounce.500ms="editingFieldValidationMin"
                                                        class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                        {{ $selectedField['type'] === 'number' ? 'Max' : 'Max Length' }}
                                                    </label>
                                                    <input type="number" 
                                                        wire:model.live.debounce.500ms="editingFieldValidationMax"
                                                        class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400">
                                                </div>
                                            </div>
                                        @endif
                                        
                                        @if(in_array($selectedField['type'], ['dropdown', 'radio', 'checkbox']))
                                            <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Options</label>
                                                <div class="space-y-2">
                                                    @foreach($editingFieldOptions as $index => $option)
                                                        <div class="flex items-center space-x-2" wire:key="option-{{ $index }}">
                                                            <input type="text" 
                                                                wire:model.live.debounce.500ms="editingFieldOptions.{{ $index }}.label" 
                                                                class="flex-1 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded shadow-sm text-xs focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400"
                                                                placeholder="Option Label">
                                                            <button wire:click="removeOption({{ $index }})" class="p-1 text-gray-400 hover:text-red-500" title="Remove Option">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                            </button>
                                                        </div>
                                                    @endforeach
                                                    <button wire:click="addOption" class="text-xs font-medium text-blue-500 dark:text-blue-400 hover:text-blue-600 dark:hover:text-blue-300 flex items-center mt-2">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                        Add Option
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="text-sm text-gray-500 dark:text-gray-400 text-center mt-10">Field not found.</div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                
            </div>
        @endif

        <!-- STEP 3: Settings -->
        @if($currentStep === 3)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-8 max-w-3xl mx-auto mt-8">
                <div class="border-b border-gray-100 dark:border-gray-700 pb-4 mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Form Settings</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Configure how and who can access your form.</p>
                </div>

                <div class="space-y-6">
                    <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-900 dark:text-gray-100">Publish Form</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Make this form accessible via public URL.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model.live="isPublished" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 dark:bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-500 dark:peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>

                <div class="mt-8 flex justify-between items-center border-t border-gray-100 dark:border-gray-700 pt-6">
                    <button wire:click="previousStep" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded hover:bg-gray-50 dark:hover:bg-gray-700 transition text-sm">&larr; Back</button>
                    <button wire:click="nextStep" class="px-6 py-2 bg-blue-500 dark:bg-blue-600 text-white rounded hover:bg-blue-600 dark:hover:bg-blue-500 shadow-sm transition font-medium">Next: Finish &rarr;</button>
                </div>
            </div>
        @endif

        <!-- STEP 4: Finish -->
        @if($currentStep === 4)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-8 max-w-3xl mx-auto mt-8 text-center">
                
                <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 text-green-500 dark:text-green-400 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">Form is ready!</h2>
                <p class="text-gray-500 dark:text-gray-400 mb-8">Your form has been saved successfully and is ready to collect responses.</p>
                
                <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg mb-8 text-left border border-gray-200 dark:border-gray-700">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Public URL</label>
                    <div class="flex">
                        <input type="text" readonly value="{{ route('form.show', $form->active_version_id ?? 0) }}" class="flex-1 border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 rounded-l-md bg-white text-sm">
                        <a href="{{ route('form.show', $form->active_version_id ?? 0) }}" target="_blank" class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-r-md border border-l-0 border-gray-300 dark:border-gray-600 hover:bg-gray-300 dark:hover:bg-gray-600 transition text-sm font-medium flex items-center">
                            Open
                        </a>
                    </div>
                </div>

                <div class="flex justify-center space-x-4">
                    <button wire:click="previousStep" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded hover:bg-gray-50 dark:hover:bg-gray-700 transition font-medium">Edit Settings</button>
                    <a href="{{ route('dashboard') }}" class="px-6 py-2 bg-blue-500 dark:bg-blue-600 text-white rounded hover:bg-blue-600 dark:hover:bg-blue-500 shadow-sm transition font-medium">Go to Dashboard</a>
                </div>
            </div>
        @endif

    </main>
    
    <!-- JSON Editor Tab Support (Hidden for now, but kept for logic) -->
    <div style="display: none;">
        <textarea wire:model.live="rawJson"></textarea>
    </div>

    <!-- Import Modal -->
    @if($showImportModal)
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] flex flex-col">
                
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Import Document</h3>
                    <button wire:click="cancelImport" class="text-gray-400 hover:text-gray-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto flex-1">
                    @if($importJob && $importJob->status === 'pending' || ($importJob && $importJob->status === 'processing'))
                        <div wire:poll.2s="checkImportJobStatus" class="flex flex-col items-center justify-center py-12">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mb-4"></div>
                            <p class="text-gray-600 dark:text-gray-300">Processing document... AI is inferring field types.</p>
                        </div>
                    @elseif($importJob && $importJob->status === 'preview')
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">Review the detected fields. AI has attempted to map the best field type for each block. You can override them before committing.</p>
                            
                            @if(count($importWarnings) > 0)
                                <div class="bg-yellow-50 dark:bg-yellow-900/30 border-l-4 border-yellow-400 p-4 mb-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm text-yellow-800 dark:text-yellow-200 font-medium">Warnings during import:</h3>
                                            <ul class="mt-1 list-disc list-inside text-xs text-yellow-700 dark:text-yellow-300">
                                                @foreach($importWarnings as $warning)
                                                    <li>{{ $warning }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-800">
                                        <tr>
                                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Original Label</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Inferred Type</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
                                        @foreach($importSchema as $index => $field)
                                            <tr>
                                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-900 dark:text-gray-100 font-medium">
                                                    {{ $field['label'] }}
                                                    @if($field['required'] ?? false) <span class="text-red-500">*</span> @endif
                                                </td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                    <select wire:model="importSchema.{{ $index }}.type" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:max-w-xs sm:text-sm sm:leading-6 dark:bg-gray-800 dark:text-gray-100 dark:ring-gray-600">
                                                        <option value="text">Short Text</option>
                                                        <option value="textarea">Long Text</option>
                                                        <option value="number">Number</option>
                                                        <option value="email">Email</option>
                                                        <option value="date">Date</option>
                                                        <option value="checkbox">Checkbox List</option>
                                                        <option value="radio">Radio Buttons</option>
                                                        <option value="dropdown">Dropdown</option>
                                                        <option value="file">File Upload</option>
                                                        <option value="rating">Rating</option>
                                                        <option value="section_heading">Section Heading</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @elseif($importJob && $importJob->status === 'failed')
                        <div class="flex flex-col items-center justify-center py-12">
                            <svg class="w-16 h-16 text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Import Failed</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 text-center max-w-md">{{ $importJob->error }}</p>
                        </div>
                    @endif
                </div>
                
                <div class="p-6 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3 bg-gray-50 dark:bg-gray-800/50 rounded-b-lg">
                    <button wire:click="cancelImport" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-50 dark:hover:bg-gray-700 transition">Cancel</button>
                    @if($importJob && $importJob->status === 'preview')
                        <button wire:click="commitImport" class="px-6 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 shadow-sm transition font-medium">Commit & Add to Form</button>
                    @endif
                </div>

            </div>
        </div>
    @endif
</div>

<div class="flex h-screen bg-gray-50 dark:bg-gray-900" x-data="{ tab: @entangle('activeTab') }">
    
    <!-- Sidebar: Field Types -->
    <div class="w-64 bg-white dark:bg-gray-800 border-r dark:border-gray-700 flex flex-col">
        <div class="p-4 border-b dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Add Fields</h2>
        </div>
        <div class="p-4 space-y-2 overflow-y-auto flex-1">
            @foreach(app(\App\Services\Builder\FieldRegistry::class)->getAllRegisteredTypes() as $type)
                <button wire:click="addField('{{ $type }}')" 
                        class="w-full text-left px-4 py-2 border rounded hover:bg-indigo-50 dark:hover:bg-indigo-900 dark:border-gray-700 dark:text-gray-200 transition">
                    + {{ ucfirst(str_replace('_', ' ', $type)) }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Main Workspace -->
    <div class="flex-1 flex flex-col overflow-hidden">
        
        <!-- Header -->
        <header class="bg-white dark:bg-gray-800 border-b dark:border-gray-700 p-4 flex justify-between items-center">
            <div class="flex space-x-4">
                <button @click="tab = 'builder'" :class="tab == 'builder' ? 'text-indigo-600 font-bold' : 'text-gray-500'">Builder</button>
                <button @click="tab = 'json'" :class="tab == 'json' ? 'text-indigo-600 font-bold' : 'text-gray-500'">JSON Editor</button>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-500" wire:loading.class="animate-pulse">{{ $saveStatus }}</span>
                <button wire:click="undo" class="p-2 bg-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300 transition" title="Undo">↩</button>
                <button wire:click="redo" class="p-2 bg-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300 transition" title="Redo">↪</button>
                <button class="px-4 py-2 bg-indigo-600 text-white rounded shadow hover:bg-indigo-700 transition">Publish</button>
            </div>
        </header>

        <!-- Builder Area -->
        <div x-show="tab == 'builder'" class="flex-1 overflow-y-auto p-8 relative flex justify-center">
            <div class="w-full max-w-3xl bg-white dark:bg-gray-800 shadow rounded-lg p-8 min-h-[500px]">
                
                @if(count($schema['fields']) === 0)
                    <div class="flex items-center justify-center h-64 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg text-gray-500">
                        Drag fields here or click in the sidebar to add.
                    </div>
                @else
                    <!-- Sortable list using simple loop for now, SortableJS can be wired via Alpine -->
                    <div class="space-y-4">
                        @foreach($schema['fields'] as $field)
                            <div wire:click="selectField('{{ $field['id'] }}')" 
                                 class="p-4 border border-transparent hover:border-indigo-300 dark:hover:border-indigo-600 rounded group relative cursor-pointer {{ $selectedFieldId === $field['id'] ? 'ring-2 ring-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'bg-gray-50 dark:bg-gray-700' }}">
                                
                                <div class="flex justify-between items-center mb-2">
                                    <label class="font-medium text-gray-700 dark:text-gray-200">
                                        {{ $field['label'] ?? 'Untitled Field' }}
                                        @if($field['required'] ?? false) <span class="text-red-500">*</span> @endif
                                    </label>
                                    
                                    <div class="hidden group-hover:flex space-x-2">
                                        <button wire:click.stop="duplicateField('{{ $field['id'] }}')" class="text-xs text-blue-500 hover:underline">Duplicate</button>
                                        <button wire:click.stop="deleteField('{{ $field['id'] }}')" class="text-xs text-red-500 hover:underline">Delete</button>
                                    </div>
                                </div>
                                
                                @if($field['type'] === 'text')
                                    <input type="text" disabled class="w-full border-gray-300 dark:border-gray-600 rounded shadow-sm bg-gray-100 dark:bg-gray-800 cursor-not-allowed" placeholder="Short text input">
                                @elseif($field['type'] === 'textarea')
                                    <textarea disabled class="w-full border-gray-300 dark:border-gray-600 rounded shadow-sm bg-gray-100 dark:bg-gray-800 cursor-not-allowed" placeholder="Long text input"></textarea>
                                @else
                                    <div class="p-2 bg-gray-200 dark:bg-gray-600 rounded text-sm text-gray-500 dark:text-gray-300">[{{ ucfirst($field['type']) }} Preview]</div>
                                @endif
                                
                            </div>
                        @endforeach
                    </div>
                @endif
                
            </div>
        </div>

        <!-- JSON Editor Area -->
        <div x-show="tab == 'json'" class="flex-1 flex flex-col bg-gray-900" style="display: none;">
            @error('json') <div class="bg-red-500 text-white p-2 text-sm">{{ $message }}</div> @enderror
            <textarea wire:model.live.debounce.1000ms="rawJson" 
                      class="flex-1 w-full bg-gray-900 text-green-400 font-mono p-4 focus:outline-none resize-none"
                      spellcheck="false"></textarea>
        </div>

    </div>

    <!-- Properties Sidebar -->
    @if($selectedFieldId)
        @php
            $selectedField = collect($schema['fields'])->firstWhere('id', $selectedFieldId);
        @endphp
        @if($selectedField)
        <div class="w-80 bg-white dark:bg-gray-800 border-l dark:border-gray-700 flex flex-col">
            <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Properties</h2>
                <button wire:click="$set('selectedFieldId', null)" class="text-gray-500 hover:text-gray-700">✕</button>
            </div>
            <div class="p-4 space-y-4 overflow-y-auto flex-1">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Label</label>
                    <input type="text" 
                           wire:model.live.debounce.500ms="schema.fields.{{ array_search($selectedField, $schema['fields']) }}.label"
                           wire:change="saveSchema"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Key (Variable Name)</label>
                    <input type="text" 
                           wire:model.live.debounce.500ms="schema.fields.{{ array_search($selectedField, $schema['fields']) }}.key"
                           wire:change="saveSchema"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                </div>
                <div class="flex items-center">
                    <input type="checkbox" 
                           wire:model.live="schema.fields.{{ array_search($selectedField, $schema['fields']) }}.required"
                           wire:change="saveSchema"
                           class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label class="ml-2 block text-sm text-gray-900 dark:text-gray-300">Required</label>
                </div>
                
                @if(in_array($selectedField['type'], ['dropdown', 'radio', 'checkbox']))
                    <div class="pt-4 border-t dark:border-gray-700">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Options</label>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Edit options in JSON editor for now.</p>
                    </div>
                @endif
            </div>
        </div>
        @endif
    @endif

</div>

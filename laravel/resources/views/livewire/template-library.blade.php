<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Template Library</h1>
    
    <div class="flex space-x-4 mb-6">
        <input type="text" wire:model.live.debounce.300ms="searchQuery" placeholder="Search templates..." class="border rounded px-4 py-2 w-full max-w-md">
        <select wire:model.live="category" class="border rounded px-4 py-2">
            <option value="">All Categories</option>
            <option value="Survey">Survey</option>
            <option value="Registration">Registration</option>
            <option value="Contact">Contact</option>
        </select>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @forelse($templates as $template)
            <div class="border rounded-lg shadow-sm p-4 bg-white dark:bg-gray-800">
                <h3 class="text-lg font-semibold">{{ $template->name }}</h3>
                <span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mt-2">{{ $template->category ?? 'General' }}</span>
                <div class="mt-4 flex justify-between items-center">
                    <span class="text-xs text-gray-500">By {{ $template->creator->name ?? 'System' }}</span>
                    <button wire:click="duplicateTemplate({{ $template->id }})" class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700">Use Template</button>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center text-gray-500 py-12">No templates found.</div>
        @endforelse
    </div>
</div>

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('My Forms') }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('templates') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded shadow hover:bg-gray-300 transition text-sm font-medium">
                    Browse Templates
                </a>
                <form method="POST" action="{{ route('forms.store') }}">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    Create New Form
                </button>
            </form>
            <livewire:create-ai-form />
        </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($forms as $form)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-gray-700 hover:shadow-md transition">
                        <div class="p-6 text-gray-900 dark:text-gray-100 flex flex-col h-full">
                            <h3 class="text-lg font-bold mb-2">{{ $form->title }}</h3>
                            <p class="text-sm text-gray-500 mb-4 flex-1">
                                Status: <span class="capitalize {{ $form->status === 'published' ? 'text-green-600' : 'text-yellow-600' }}">{{ $form->status }}</span>
                                <br>Updated {{ $form->updated_at->diffForHumans() }}
                            </p>
                            <div class="mt-4 flex space-x-3 pt-4 border-t dark:border-gray-700">
                                <a href="{{ route('builder', $form->id) }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Builder</a>
                                <a href="{{ route('form.dashboard', $form->id) }}" class="text-sm text-gray-600 hover:text-gray-800 font-medium">Dashboard</a>
                                @if($form->active_version_id)
                                    <a href="{{ route('form.show', $form->active_version_id) }}" target="_blank" class="text-sm text-green-600 hover:text-green-800 font-medium ml-auto">Preview &rarr;</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-12 bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No forms yet</h3>
                        <p class="text-gray-500 mb-4">Get started by creating a blank form or browsing our templates.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>

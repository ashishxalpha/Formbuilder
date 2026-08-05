<div class="max-w-3xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    @if($isSubmitted)
        <div class="bg-white shadow rounded-lg p-8 text-center">
            <h2 class="text-2xl font-bold text-green-600 mb-4">Thank You!</h2>
            <p class="text-gray-600">Your submission has been received.</p>
        </div>
    @else
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-4 py-5 border-b border-gray-200 sm:px-6 bg-indigo-50">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    {{ $compiledData['compiled_schema']['metadata']['title'] ?? 'Form' }}
                </h3>
                @if(isset($compiledData['compiled_schema']['metadata']['description']))
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        {{ $compiledData['compiled_schema']['metadata']['description'] }}
                    </p>
                @endif
            </div>
            
            <form wire:submit.prevent="submit" class="px-4 py-5 sm:p-6 space-y-6">
                @foreach($compiledData['compiled_schema']['fields'] as $field)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            {{ $field['label'] }}
                            @if($field['required'] ?? false) <span class="text-red-500">*</span> @endif
                        </label>
                        
                        <div class="mt-1">
                            @if($field['type'] === 'text' || $field['type'] === 'email' || $field['type'] === 'number' || $field['type'] === 'phone' || $field['type'] === 'date')
                                <input type="{{ $field['type'] === 'text' ? 'text' : $field['type'] }}" 
                                       wire:model="formData.{{ $field['key'] }}"
                                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            
                            @elseif($field['type'] === 'textarea')
                                <textarea wire:model="formData.{{ $field['key'] }}" rows="3"
                                          class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                            
                            @elseif($field['type'] === 'dropdown')
                                <select wire:model="formData.{{ $field['key'] }}"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    <option value="">Select an option...</option>
                                    @foreach($field['options'] ?? [] as $option)
                                        <option value="{{ $option['value'] ?? $option['label'] }}">{{ $option['label'] }}</option>
                                    @endforeach
                                </select>
                                
                            @elseif($field['type'] === 'radio')
                                <div class="space-y-2">
                                    @foreach($field['options'] ?? [] as $option)
                                        <div class="flex items-center">
                                            <input type="radio" wire:model="formData.{{ $field['key'] }}" value="{{ $option['value'] ?? $option['label'] }}"
                                                   class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                            <label class="ml-3 block text-sm font-medium text-gray-700">
                                                {{ $option['label'] }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                
                            @elseif($field['type'] === 'checkbox')
                                <div class="space-y-2">
                                    @foreach($field['options'] ?? [] as $option)
                                        <div class="flex items-center">
                                            <input type="checkbox" wire:model="formData.{{ $field['key'] }}" value="{{ $option['value'] ?? $option['label'] }}"
                                                   class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                            <label class="ml-3 block text-sm font-medium text-gray-700">
                                                {{ $option['label'] }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                
                            @elseif($field['type'] === 'file')
                                <input type="file" wire:model="formData.{{ $field['key'] }}" class="block w-full text-sm text-gray-500
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-full file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-indigo-50 file:text-indigo-700
                                  hover:file:bg-indigo-100
                                ">
                                
                            @elseif($field['type'] === 'rating')
                                <input type="number" wire:model="formData.{{ $field['key'] }}" min="1" max="{{ $field['options']['max'] ?? 5 }}"
                                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-32 sm:text-sm border-gray-300 rounded-md">
                                       
                            @elseif($field['type'] === 'section_heading')
                                <h4 class="text-md font-bold text-gray-800 mt-6 border-b pb-2">{{ $field['label'] }}</h4>
                            @endif
                        </div>
                        @error('formData.'.$field['key']) <span class="text-sm text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                @endforeach
                
                <div class="pt-5">
                    <div class="flex justify-end">
                        <button type="submit"
                                class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Submit Response
                        </button>
                    </div>
                </div>
            </form>
        </div>
    @endif
</div>

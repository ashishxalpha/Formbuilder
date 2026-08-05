<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $form->title }} Dashboard</h1>
        <a href="{{ route('builder', ['form' => $form->id]) }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Open Builder</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <!-- Activity Timeline -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-100">Activity Timeline</h2>
            <div class="flow-root">
                <ul role="list" class="-mb-8">
                    @forelse($activities as $activity)
                    <li>
                        <div class="relative pb-8">
                            <div class="relative flex space-x-3">
                                <div>
                                    <span class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1 pt-1 flex justify-between space-x-4">
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ ucfirst($activity->action) }} by <span class="font-medium text-gray-900 dark:text-gray-200">{{ $activity->user->name ?? 'System' }}</span>
                                        </p>
                                    </div>
                                    <div class="text-right text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                        <time datetime="{{ $activity->created_at }}">{{ $activity->created_at->diffForHumans() }}</time>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    @empty
                    <li class="text-gray-500 text-sm">No recent activity.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- AI Job Status -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-100">AI Job Status</h2>
            <div class="space-y-4">
                @forelse($aiJobs as $job)
                    <div class="p-4 border rounded-lg dark:border-gray-700">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-medium text-gray-800 dark:text-gray-200">{{ ucfirst($job->provider ?? 'AI Provider') }} - {{ $job->model }}</span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $job->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                  ($job->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($job->status) }}
                            </span>
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 grid grid-cols-2 gap-2">
                            <div>Latency: {{ $job->latency_ms }}ms</div>
                            <div>Tokens: {{ $job->input_tokens }} in / {{ $job->output_tokens }} out</div>
                            <div>Retries: {{ $job->retries }}</div>
                            <div>Cost: ${{ number_format($job->cost, 4) }}</div>
                        </div>
                        @if($job->error)
                            <div class="mt-2 text-xs text-red-600 bg-red-50 p-2 rounded">
                                {{ Str::limit($job->error, 100) }}
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-gray-500 text-sm">No AI jobs found for this form.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Submissions Table -->
    <div class="mt-8 bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Submissions</h2>
            <div class="flex space-x-3">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search responses..." class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm text-sm">
                <button wire:click="exportCsv" class="px-4 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition">Export CSV</button>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted At</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-full">Data Snapshot</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($submissions as $sub)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">#{{ $sub->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $sub->created_at->format('M d, Y H:i') }}</td>
                            <td class="px-6 py-4 text-sm">
                                <div class="truncate max-w-2xl">
                                    {!! $this->formatSubmissionData($sub->response_data) !!}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No submissions found yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            {{ $submissions->links() }}
        </div>
    </div>
</div>

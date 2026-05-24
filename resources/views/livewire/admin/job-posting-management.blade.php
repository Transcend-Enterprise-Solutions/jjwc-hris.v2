<div class="container mx-auto px-4 py-6" x-data="{
    isJobModalOpen: @entangle('isJobModalOpen'),
    isDeleteModalOpen: @entangle('isDeleteModalOpen'),
    isApplicationModalOpen: @entangle('isApplicationModalOpen')
}">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">

        <!-- Header Section -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-6 text-white">
            <div class="max-w-7xl mx-auto space-y-5">
                <!-- First Row - Centered Title -->
                <div class="flex justify-center">
                    <h1 class="text-3xl font-bold">Job Management</h1>
                </div>

                <!-- Second Row - Search Controls -->
                <div class="flex flex-col sm:flex-row justify-center items-center gap-3 w-full">
                    <!-- Search Input -->
                    <div class="relative w-full sm:w-64">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" wire:model.debounce.500ms="search"
                            class="block w-full pl-10 pr-3 py-2 bg-blue-500 bg-opacity-20 border border-blue-400 border-opacity-30 rounded-lg placeholder-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-transparent"
                            placeholder="Search jobs...">
                    </div>

                    <!-- Filter Dropdown -->
                    <select wire:model="statusFilter"
                            class="w-full sm:w-auto bg-blue-500 bg-opacity-20 border border-blue-400 border-opacity-30 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-transparent">
                        <option value="all">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>

                    <!-- Add Button -->
                    <button wire:click="openJobModal"
                            class="w-full sm:w-auto flex items-center justify-center gap-2 bg-white text-blue-600 hover:bg-blue-50 px-4 py-2 rounded-lg font-medium transition-colors duration-200 whitespace-nowrap">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Add New
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="p-6">
            @if(!$showApplicants)
                <!-- Jobs Table -->
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Job Details</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Position & Grade</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Department</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Applications</th>
                                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($jobs as $job)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150">
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $job->posting_title }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $job->announcement_number }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            Closes: {{ $job->closing_date ? $job->closing_date->format('M d, Y') : 'Not set' }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 dark:text-white">{{ $job->position }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Grade: {{ $job->job_grade }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $job->number_of_vacancies }} vacancy(ies)</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 dark:text-white">{{ $job->division_office_department }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">₱{{ number_format($job->salary, 2) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full
                                        {{ $job->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' }}">
                                        {{ $job->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $job->applications_count }}</span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400 ml-1">applicants</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end space-x-2">
                                        @foreach([
                                            ['viewApplications', $job->id, 'eye', 'View Applications', 'text-blue-600 hover:text-blue-800'],
                                            ['editJob', $job->id, 'pencil', 'Edit', 'text-indigo-600 hover:text-indigo-800'],
                                            ['toggleStatus', $job->id, 'cog', $job->is_active ? 'Deactivate' : 'Activate', 'text-yellow-600 hover:text-yellow-800'],
                                            ['confirmDelete', $job->id, 'trash', 'Delete', 'text-red-600 hover:text-red-800']
                                        ] as [$method, $param, $icon, $title, $class])
                                            <button wire:click="{{ $method }}({{ $param }})"
                                                    class="{{ $class }} p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition duration-150"
                                                    title="{{ $title }}">
                                                @if($icon === 'eye')
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                @elseif($icon === 'pencil')
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                @elseif($icon === 'cog')
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                @else
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                @endif
                                            </button>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="text-gray-500 dark:text-gray-400">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6" />
                                        </svg>
                                        <p class="mt-2 text-sm">No job postings found</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $jobs->links() }}
                </div>

            @else
                <!-- Applications Section -->
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                    <!-- Applications Header -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-6 border-b dark:border-gray-700 gap-4">
                        <div class="flex items-center space-x-4">
                            <button wire:click="$set('showApplicants', false)"
                                    class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition duration-150">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                            </button>
                            <div>
                                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Applications</h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ \App\Models\JobPosting::find($currentJobId)->posting_title }}
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3">
                            <select wire:model.live="applicationStatus" wire:change="loadApplications"
                                    class="border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500">
                                <option value="all">All Applications</option>
                                @foreach(['pending', 'viewed', 'for interview', 'waiting', 'hired', 'rejected'] as $status)
                                    <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>

                            <button wire:click="exportApplications({{ $currentJobId }})"
                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2 transition duration-200">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Export
                            </button>
                        </div>
                    </div>

                    <!-- Status Summary -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 flex flex-wrap gap-2">
                        @php
                            $statuses = [
                                'pending' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                'viewed' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                                'for interview' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                'waiting' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
                                'hired' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                            ];
                        @endphp

                        @foreach($statuses as $status => $class)
                            <button wire:click="$set('applicationStatus', '{{ $status }}')" wire:click="loadApplications"
                                    class="px-3 py-1.5 rounded-full text-xs font-medium cursor-pointer transition duration-150
                                        {{ $class }}
                                        {{ $applicationStatus === $status ? 'ring-2 ring-offset-1 ring-gray-400 dark:ring-gray-500' : '' }}
                                        hover:ring-2 hover:ring-offset-1 hover:ring-gray-300">
                                {{ ucfirst($status) }}: {{ $applicationCounts[$status] ?? 0 }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Applications Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Applicant</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Applied</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Resume</th>
                                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($applications as $application)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <!-- Default avatar since no profile photo is stored -->
                                            <div class="h-10 w-10 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    {{ strtoupper(substr($application->first_name, 0, 1) . substr($application->last_name, 0, 1)) }}
                                                </span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $application->full_name }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-300">
                                                    {{ $application->email }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-300">
                                                    {{ $application->tracking_code }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">
                                        {{ $application->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full
                                        @switch($application->status)
                                            @case('pending')
                                                bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                @break
                                            @case('viewed')
                                                bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                @break
                                            @case('for interview')
                                                bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                                @break
                                            @case('waiting')
                                                bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200
                                                @break
                                            @case('hired')
                                                bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                @break
                                            @case('rejected')
                                                bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                                @break
                                            @default
                                                bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                        @endswitch">
                                        {{ $application->status_label }}
                                    </span>
                                </td>
                                    <td class="px-6 py-4">
                                        @if($application->resume_path)
                                            <a href="{{ route('download.document', [
                                                'path' => base64_encode($application->resume_path),
                                                'name' => basename($application->resume_path)
                                            ]) }}"
                                            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-sm font-medium">
                                                View Resume
                                            </a>


                                        @else
                                            <span class="text-gray-400 text-sm">No resume</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button wire:click="viewApplicationDetails({{ $application->id }})"
                                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition duration-200">
                                            View Details
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="text-gray-500 dark:text-gray-400">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7a3 3 0 01-3-3v-2a3 3 0 015.356-1.857M17 20v-2a3 3 0 00-3 3v2M12 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                            <p class="mt-2 text-sm">No applications found</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Job Modal -->
    <x-modal id="jobModal" maxWidth="5xl" wire:model="isJobModalOpen">
        <div class="p-6">
            <div class="mb-6 py-4 dark:text-gray-50 text-slate-900 font-bold text-xl">
                {{ $isEditMode ? 'Edit Job Posting' : 'Create New Job Posting' }}
                <button @click="show = false" class="float-right focus:outline-none" wire:click="resetVariables">
                    <i class="fas fa-times text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"></i>
                </button>
            </div>

            <form wire:submit.prevent="saveJob" class="space-y-6">
                <!-- Basic Information Section -->
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h3>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Announcement Number</label>
                                <input wire:model="announcement_number" type="text" placeholder="e.g. ANN-2025-001"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                @error('announcement_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Opening Date</label>
                                <input wire:model="opening_date" type="date"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                @error('opening_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Closing Date</label>
                                <input wire:model="closing_date" type="date"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                @error('closing_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Posting Title</label>
                                <input wire:model="posting_title" type="text" placeholder="e.g. Senior Software Developer Position"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                @error('posting_title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Position</label>
                                <input wire:model="position" type="text" placeholder="e.g. Software Developer"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                @error('position') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Salary Grade</label>
                                <input wire:model="job_grade" type="text" placeholder="e.g. SG-15, Level 3, etc."
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                @error('job_grade') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Number of Vacancies</label>
                                <input wire:model="number_of_vacancies" type="number" min="1" placeholder="1"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                @error('number_of_vacancies') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Division/Office/Department</label>
                                <input wire:model="division_office_department" type="text" placeholder="e.g. Information Technology Department"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                @error('division_office_department') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Salary -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Salary</label>
                        <input wire:model="salary" type="number" step="0.01" placeholder="e.g. 25000.00"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        @error('salary') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Active Status -->
                    <div class="mt-4 flex items-center">
                        <input wire:model="is_active" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active Job Posting</label>
                    </div>
                </div>

                <!-- Job Details Section -->
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Job Details</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Job Summary</label>
                            <textarea wire:model="job_summary" rows="4" placeholder="Brief overview of the position and its purpose..."
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"></textarea>
                            @error('job_summary') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Duties and Responsibilities</label>
                            <textarea wire:model="duties_and_responsibilities" rows="6" placeholder="List the main duties and responsibilities..."
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"></textarea>
                            @error('duties_and_responsibilities') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- File Attachment Section -->
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Job Attachment</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Upload Job Details (PDF Only)</label>
                            <input wire:model="attachment" type="file" accept=".pdf"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Supported format: PDF only (Max: 5MB)</p>
                            @error('attachment') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        @if($existing_attachment_path)
                            <div class="flex items-center space-x-2 p-3 bg-blue-50 dark:bg-blue-900 rounded-lg">
                                <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="text-sm text-blue-700 dark:text-blue-300">Current attachment:</span>

                                <!-- Download Button -->
                                <button type="button" wire:click="downloadAttachment('{{ $existing_attachment_path }}')"
                                        class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 underline">
                                    {{ basename($existing_attachment_path) }}
                                </button>

                                <!-- Remove Button -->
                                <button type="button" wire:click="removeAttachment"
                                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                        title="Remove attachment">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        @endif

                        @if($attachment)
                            <div class="flex items-center space-x-2 p-3 bg-green-50 dark:bg-green-900 rounded-lg">
                                <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-sm text-green-700 dark:text-green-300">New file selected:</span>
                                <span class="text-sm text-green-600 dark:text-green-400">{{ $attachment->getClientOriginalName() }}</span>

                                <!-- File size display -->
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    ({{ number_format($attachment->getSize() / 1024, 2) }} KB)
                                </span>

                                <!-- Remove selected file button -->
                                <button type="button" wire:click="$set('attachment', null)"
                                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                        title="Remove selected file">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>



                <!-- Modal Footer -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" @click="show = false" wire:click="resetVariables"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-200">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition duration-200 flex items-center space-x-2">
                        <span wire:loading.remove wire:target="saveJob">{{ $isEditMode ? 'Update Job' : 'Create Job' }}</span>
                        <span wire:loading wire:target="saveJob" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- Delete Confirmation Modal -->
    <x-modal id="deleteModal" maxWidth="lg" wire:model="isDeleteModalOpen">
        <div class="p-6">
            <div class="mb-4 py-2 dark:text-gray-50 text-slate-900 font-bold text-lg">
                Delete Job Posting
                <button @click="show = false" class="float-right focus:outline-none" wire:click="resetVariables">
                    <i class="fas fa-times text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"></i>
                </button>
            </div>

            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Are you sure?</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        This action cannot be undone. This will permanently delete the job posting and all associated applications.
                    </p>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-6 mt-6 border-t border-gray-200 dark:border-gray-700">
                <button @click="show = false" wire:click="resetVariables"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-200">
                    Cancel
                </button>
                <button wire:click="deleteJob"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition duration-200">
                    <i class="fas fa-trash mr-2"></i>Delete
                </button>
            </div>
        </div>
    </x-modal>

    <!-- Application Details Modal -->
    <x-modal id="applicationModal" maxWidth="3xl" wire:model.live="isApplicationModalOpen">
        <div class="p-6">
            <div class="mb-6 py-4 dark:text-gray-50 text-slate-900 font-bold text-xl">
                Application Details
                <button @click="show = false" class="float-right focus:outline-none" wire:click="resetVariables">
                    <i class="fas fa-times text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"></i>
                </button>
            </div>

            @if($selectedApplication)
            <div class="space-y-6">
                <!-- Applicant Info -->
                <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="h-12 w-12 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                        <span class="text-lg font-medium text-gray-700 dark:text-gray-300">
                            {{ strtoupper(substr($selectedApplication->first_name, 0, 1) . substr($selectedApplication->last_name, 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $selectedApplication->full_name }}</h4>
                        <p class="text-gray-600 dark:text-gray-400">{{ $selectedApplication->email }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Applied on {{ $selectedApplication->created_at->format('M d, Y') }}</p>
                    </div>
                </div>

                <!-- Additional Applicant Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                        <p class="text-gray-900 dark:text-white">{{ $selectedApplication->phone ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Address</label>
                        <p class="text-gray-900 dark:text-white">{{ $selectedApplication->address ?? 'Not provided' }}</p>
                    </div>
                    @if($selectedApplication->middle_name)
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Middle Name</label>
                        <p class="text-gray-900 dark:text-white">{{ $selectedApplication->middle_name }}</p>
                    </div>
                    @endif
                </div>

                <!-- Job Position Info -->
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <h5 class="text-lg font-medium text-blue-900 dark:text-blue-200 mb-2">Applied Position</h5>
                    <p class="text-blue-800 dark:text-blue-300 font-semibold">{{ $selectedApplication->jobPosting->posting_title }}</p>
                    <p class="text-sm text-blue-600 dark:text-blue-400">{{ $selectedApplication->jobPosting->position }} - {{ $selectedApplication->jobPosting->job_grade }}</p>
                </div>

                <!-- Cover Letter -->
                @if($selectedApplication->cover_letter)
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Cover Letter</label>
                    <div class="p-4 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg max-h-40 overflow-y-auto">
                        <p class="text-gray-900 dark:text-white whitespace-pre-wrap text-sm">{{ $selectedApplication->cover_letter }}</p>
                    </div>
                </div>
                @endif

                <!-- Status Update Form -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Application Status</label>
                        <select wire:model.live="currentStatus"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            @foreach(['pending', 'viewed', 'for interview', 'waiting', 'hired', 'rejected'] as $status)
                                <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Interview Details (shown only when currentStatus is 'for interview') -->
                    @if($currentStatus === 'for interview')
                    <div class="space-y-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                        <h5 class="text-lg font-medium text-yellow-800 dark:text-yellow-200 mb-2">Interview Details</h5>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Interview Date & Time <span class="text-red-500">*</span></label>
                            <input type="datetime-local" wire:model="interviewDate"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            @error('interviewDate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Interview Location <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="interviewLocation" placeholder="Enter interview location"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            @error('interviewLocation') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Interview Notes <span class="text-red-500">*</span></label>
                            <textarea wire:model="interviewNotes" rows="3" placeholder="Add any notes about the interview..."
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"></textarea>
                            @error('interviewNotes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status Notes</label>
                        <textarea wire:model="statusNotes" rows="3" placeholder="Add notes about this application..."
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"></textarea>
                        @error('statusNotes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Documents Section -->
                <div class="space-y-3">
                    <h5 class="text-lg font-medium text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Submitted Documents</h5>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <!-- Resume -->
                        <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-file-pdf text-red-500 mr-3"></i>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Resume</span>
                            </div>
                            @if($selectedApplication->resume_path)
                                <a href="{{ route('download.document', ['path' => base64_encode($selectedApplication->resume_path), 'name' => 'Resume']) }}"
                                class="inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-sm rounded transition duration-200">
                                    <i class="fas fa-download mr-1"></i>Download
                                </a>
                            @else
                                <span class="text-gray-400 text-sm">Not provided</span>
                            @endif
                        </div>

                        <!-- PDS -->
                        <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-file-alt text-blue-500 mr-3"></i>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">PDS</span>
                            </div>
                            @if($selectedApplication->pds_path)
                                <a href="{{ route('download.document', ['path' => base64_encode($selectedApplication->pds_path), 'name' => 'PDS']) }}"
                                class="inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-sm rounded transition duration-200">
                                    <i class="fas fa-download mr-1"></i>Download
                                </a>
                            @else
                                <span class="text-gray-400 text-sm">Not provided</span>
                            @endif
                        </div>

                        <!-- Transcript -->
                        <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-graduation-cap text-green-500 mr-3"></i>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Transcript</span>
                            </div>
                            @if($selectedApplication->transcript_path)
                                <a href="{{ route('download.document', ['path' => base64_encode($selectedApplication->transcript_path), 'name' => 'Transcript']) }}"
                                class="inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-sm rounded transition duration-200">
                                    <i class="fas fa-download mr-1"></i>Download
                                </a>
                            @else
                                <span class="text-gray-400 text-sm">Not provided</span>
                            @endif
                        </div>

                        <!-- Diploma -->
                        <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-certificate text-yellow-500 mr-3"></i>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Diploma</span>
                            </div>
                            @if($selectedApplication->diploma_path)
                                <a href="{{ route('download.document', ['path' => base64_encode($selectedApplication->diploma_path), 'name' => 'Diploma']) }}"
                                class="inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-sm rounded transition duration-200">
                                    <i class="fas fa-download mr-1"></i>Download
                                </a>
                            @else
                                <span class="text-gray-400 text-sm">Not provided</span>
                            @endif
                        </div>

                        <!-- PRC License -->
                        <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-id-card text-purple-500 mr-3"></i>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">PRC License</span>
                            </div>
                            @if($selectedApplication->prc_license_path)
                                <a href="{{ route('download.document', ['path' => base64_encode($selectedApplication->prc_license_path), 'name' => 'PRC_License']) }}"
                                class="inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-sm rounded transition duration-200">
                                    <i class="fas fa-download mr-1"></i>Download
                                </a>
                            @else
                                <span class="text-gray-400 text-sm">Not provided</span>
                            @endif
                        </div>

                        <!-- Birth Certificate -->
                        <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-baby text-pink-500 mr-3"></i>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Birth Certificate</span>
                            </div>
                            @if($selectedApplication->birth_cert_path)
                                <a href="{{ route('download.document', ['path' => base64_encode($selectedApplication->birth_cert_path), 'name' => 'Birth_Certificate']) }}"
                                class="inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-sm rounded transition duration-200">
                                    <i class="fas fa-download mr-1"></i>Download
                                </a>
                            @else
                                <span class="text-gray-400 text-sm">Not provided</span>
                            @endif
                        </div>

                        <!-- Barangay Clearance -->
                        <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-home text-indigo-500 mr-3"></i>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Barangay Clearance</span>
                            </div>
                            @if($selectedApplication->barangay_clearance_path)
                                <a href="{{ route('download.document', ['path' => base64_encode($selectedApplication->barangay_clearance_path), 'name' => 'Barangay_Clearance']) }}"
                                class="inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-sm rounded transition duration-200">
                                    <i class="fas fa-download mr-1"></i>Download
                                </a>
                            @else
                                <span class="text-gray-400 text-sm">Not provided</span>
                            @endif
                        </div>

                        <!-- Police Clearance -->
                        <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-shield-alt text-blue-600 mr-3"></i>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Police Clearance</span>
                            </div>
                            @if($selectedApplication->police_clearance_path)
                                <a href="{{ route('download.document', ['path' => base64_encode($selectedApplication->police_clearance_path), 'name' => 'Police_Clearance']) }}"
                                class="inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-sm rounded transition duration-200">
                                    <i class="fas fa-download mr-1"></i>Download
                                </a>
                            @else
                                <span class="text-gray-400 text-sm">Not provided</span>
                            @endif
                        </div>

                        <!-- Relative Declaration -->
                        <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-users text-orange-500 mr-3"></i>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Relative Declaration</span>
                            </div>
                            @if($selectedApplication->relative_declaration_path)
                                <a href="{{ route('download.document', ['path' => base64_encode($selectedApplication->relative_declaration_path), 'name' => 'Relative_Declaration']) }}"
                                class="inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-sm rounded transition duration-200">
                                    <i class="fas fa-download mr-1"></i>Download
                                </a>
                            @else
                                <span class="text-gray-400 text-sm">Not provided</span>
                            @endif
                        </div>
                    </div>

                    <!-- Employment Certificates -->
                    @if($selectedApplication->employment_certs_path && is_array($selectedApplication->employment_certs_path) && count($selectedApplication->employment_certs_path) > 0)
                    <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-briefcase text-purple-500 mr-3"></i>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Employment Certificates ({{ count($selectedApplication->employment_certs_path) }})</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            @foreach($selectedApplication->employment_certs_path as $index => $certPath)
                                <div class="flex items-center justify-between p-2 bg-white dark:bg-gray-800 rounded border">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Certificate {{ $index + 1 }}</span>
                                    <a href="{{ route('download.document', ['path' => base64_encode($certPath), 'name' => 'Employment_Certificate_' . ($index + 1)]) }}"
                                    class="inline-flex items-center px-2 py-1 bg-green-600 hover:bg-green-700 text-white text-xs rounded transition duration-200">
                                        <i class="fas fa-download mr-1"></i>Download
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Training Certificates -->
                    @if($selectedApplication->training_certs_path && is_array($selectedApplication->training_certs_path) && count($selectedApplication->training_certs_path) > 0)
                    <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-award text-indigo-500 mr-3"></i>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Training Certificates ({{ count($selectedApplication->training_certs_path) }})</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            @foreach($selectedApplication->training_certs_path as $index => $certPath)
                                <div class="flex items-center justify-between p-2 bg-white dark:bg-gray-800 rounded border">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Certificate {{ $index + 1 }}</span>
                                    <a href="{{ route('download.document', ['path' => base64_encode($certPath), 'name' => 'Training_Certificate_' . ($index + 1)]) }}"
                                    class="inline-flex items-center px-2 py-1 bg-green-600 hover:bg-green-700 text-white text-xs rounded transition duration-200">
                                        <i class="fas fa-download mr-1"></i>Download
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end space-x-3 pt-6 mt-6 border-t border-gray-200 dark:border-gray-700">
                <button @click="show = false" wire:click="resetVariables"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-200">
                    <i class="fas fa-times mr-2"></i>Cancel
                </button>
                <button wire:click="updateApplicationStatus"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition duration-200">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
            </div>
            @endif
        </div>
    </x-modal>
</div>

<!-- File validation script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.querySelector('input[type="file"][wire\\:model="attachment"]');

    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];

            if (file) {
                // Check file type
                if (file.type !== 'application/pdf') {
                    alert('Please select a PDF file only.');
                    e.target.value = '';
                    return;
                }

                // Check file size (5MB = 5 * 1024 * 1024 bytes)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File size must not exceed 5MB.');
                    e.target.value = '';
                    return;
                }
            }
        });
    }
});
</script>

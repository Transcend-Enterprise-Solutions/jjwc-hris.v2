<div class="max-w-4xl mx-auto p-6 bg-white py-8 rounded-lg shadow-md mt-20">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Track Your Application</h1>
        <p class="text-gray-600">Enter your tracking code to view the status of your job application.</p>
    </div>

    <!-- Tracking Form -->
    <div class="bg-gray-50 rounded-lg p-10 mb-8">
        <form wire:submit.prevent="trackApplication" class="space-y-4">
            <div>
                <label for="trackingCode" class="block text-sm font-medium text-gray-700 mb-2">
                    Tracking Code
                </label>
                <div class="flex space-x-3">
                    <input
                        type="text"
                        id="trackingCode"
                        wire:model="trackingCode"
                        placeholder="e.g., APP-9669-EDET"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('trackingCode') border-red-500 @enderror"
                    >
                    <button
                        type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove>Track</span>
                        <span wire:loading>Searching...</span>
                    </button>
                </div>
                @error('trackingCode')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror


            </div>
        </form>

        @if($error)
            <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-md">
                <div class="flex">
                    <svg class="w-5 h-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <p class="text-sm text-red-600">{{ $error }}</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Application Details -->
    @if($application && $jobPosting)
        <div class="space-y-8">
            <!-- Status Progress -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Application Status</h2>

                <!-- Status Badge with Details -->
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-base font-medium text-gray-700">Current Status</span>
                        <span class="px-4 py-1.5 rounded-full text-sm font-semibold {{ $this->getStatusColorClass() }}">
                            {{ $application->status_label }}
                        </span>
                    </div>

                    @if($application->status_notes)
                        <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-100">
                            <p class="text-sm text-gray-700">
                                <strong class="text-blue-600">Admin Notes:</strong> {{ $application->status_notes }}
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Progress Steps -->
                <div class="relative">
                    @if($application->status !== 'rejected')
                        <div class="space-y-6">
                            @foreach($this->getStatusSteps() as $index => $step)
                                <div class="flex items-start gap-4">
                                    <!-- Step Indicator -->
                                    <div class="flex flex-col items-center">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center border-2 {{ $step['completed'] ? 'bg-green-500 border-green-500 text-white' : ($step['active'] ? 'border-blue-500 bg-white text-blue-500' : 'border-gray-200 bg-gray-50 text-gray-400') }}">
                                            @if($step['completed'])
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            @else
                                                <span class="text-xs font-medium">{{ $index + 1 }}</span>
                                            @endif
                                        </div>
                                        @if(!$loop->last)
                                            <div class="w-0.5 h-10 my-1 {{ $step['completed'] ? 'bg-green-500' : 'bg-gray-200' }}"></div>
                                        @endif
                                    </div>

                                    <!-- Step Content -->
                                    <div class="pt-1">
                                        <h3 class="text-sm font-semibold {{ $step['active'] ? 'text-blue-600' : ($step['completed'] ? 'text-green-600' : 'text-gray-500') }}">
                                            {{ $step['label'] }}
                                        </h3>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center p-6 bg-red-50 rounded-lg border border-red-100">
                            <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-red-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-red-800 mb-1">Application Not Successful</h3>
                            <p class="text-red-600 text-sm">We appreciate your time and effort in applying for this position.</p>
                            @if($application->status_notes)
                                <p class="mt-3 text-sm text-gray-600">Feedback: {{ $application->status_notes }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Interview Information -->
            @if($application->interview_date)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-blue-900 mb-4">Interview Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-blue-700">Date & Time</p>
                            <p class="text-blue-900">{{ $application->interview_date->format('F j, Y \a\t g:i A') }}</p>
                        </div>
                        @if($application->interview_location)
                            <div>
                                <p class="text-sm font-medium text-blue-700">Location</p>
                                <p class="text-blue-900">{{ $application->interview_location }}</p>
                            </div>
                        @endif
                    </div>
                    @if($application->interview_notes)
                        <div class="mt-4">
                            <p class="text-sm font-medium text-blue-700">Additional Notes</p>
                            <p class="text-blue-900">{{ $application->interview_notes }}</p>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Applicant Information -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Personal Details -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                            <dd class="text-sm text-gray-900">{{ $application->full_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="text-sm text-gray-900">{{ $application->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Phone</dt>
                            <dd class="text-sm text-gray-900">{{ $application->phone }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Application ID</dt>
                            <dd class="text-sm text-gray-900">#{{ $application->id }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Job Details -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Position Applied For</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Position</dt>
                            <dd class="text-sm text-gray-900">{{ $jobPosting->position }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Salary Grade</dt>
                            <dd class="text-sm text-gray-900">{{ $jobPosting->job_grade }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Department</dt>
                            <dd class="text-sm text-gray-900">{{ $jobPosting->division_office_department }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Announcement Number</dt>
                            <dd class="text-sm text-gray-900">{{ $jobPosting->announcement_number }}</dd>
                        </div>
                        @if($jobPosting->salary)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Salary</dt>
                                <dd class="text-sm text-gray-900">{{ $jobPosting->salary }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Documents Submitted -->
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Documents Submitted</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @php
                        $documents = [
                            'resume_path' => 'Resume',
                            'pds_path' => 'Personal Data Sheet',
                            'transcript_path' => 'Transcript of Records',
                            'diploma_path' => 'Diploma',
                            'employment_certs_path' => 'Employment Certificates',
                            'training_certs_path' => 'Training Certificates',
                            'prc_license_path' => 'PRC License',
                            'birth_cert_path' => 'Birth Certificate',
                            'barangay_clearance_path' => 'Barangay Clearance',
                            'police_clearance_path' => 'Police Clearance',
                            'relative_declaration_path' => 'Relative Declaration'
                        ];
                    @endphp

                    @foreach($documents as $field => $label)
                        <div class="flex items-center space-x-2">
                            @if($application->$field)
                                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                            <span class="text-sm {{ $application->$field ? 'text-gray-900' : 'text-gray-400' }}">
                                {{ $label }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Cover Letter -->
            @if($application->cover_letter)
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Cover Letter</h3>
                    <div class="prose max-w-none text-sm text-gray-700 bg-gray-50 p-4 rounded-md">
                        {!! nl2br(e($application->cover_letter)) !!}
                    </div>
                </div>
            @endif

            <!-- Reset Button -->
            <div class="text-center">
                <button
                    wire:click="resetTracking"
                    class="px-6 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                >
                    Track Another Application
                </button>
            </div>
        </div>
    @endif
</div>

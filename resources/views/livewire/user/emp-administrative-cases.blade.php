<div class="w-full">
    <div class="w-full bg-white rounded-2xl p-3 sm:p-8 shadow dark:bg-gray-800">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-lg font-bold text-slate-800 dark:text-white">My Administrative Cases</h1>
        </div>

        <div class="flex justify-start items-center mb-4">
            <div class="relative inline-block text-left mb-4 w-full">
                <label for="caseSearch" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Search</label>
                <input type="search" id="caseSearch" wire:model.live="caseSearch" placeholder="Search my cases..."
                    class="py-2 px-3 block w-full sm:w-80 shadow-sm text-sm font-medium border-gray-400
                                       rounded-md dark:text-gray-300 dark:bg-gray-800 outline-none focus:outline-none">
            </div>
        </div>

        <div class="overflow-x-auto">
            <div class="overflow-hidden border dark:border-gray-700 rounded-lg">
                <div class="inline-block w-full align-middle">
                    <div class="overflow-hidden border dark:border-gray-700 rounded-lg text-xs">
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white dark:bg-gray-800">
                                <thead class="bg-gray-100 dark:bg-gray-700">
                                    <tr>
                                        <!-- Case Information Group -->
                                        <th colspan="5"
                                            class="px-4 py-2 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider text-center bg-gray-100 dark:bg-gray-700 border-l border-b border-gray-300 dark:border-gray-600">
                                            Case Information
                                        </th>
                                        <!-- Response Status Group -->
                                        <th colspan="2"
                                            class="px-4 py-2 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider text-center bg-gray-100 dark:bg-gray-700 border-l border-b border-gray-300 dark:border-gray-600">
                                            Response Status
                                        </th>
                                        <!-- Corrective Action Group -->
                                        <th colspan="4"
                                            class="px-4 py-2 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider text-center bg-gray-100 dark:bg-gray-700 border-l border-b border-gray-300 dark:border-gray-600">
                                            Corrective Action Timeline
                                        </th>
                                        <!-- Actions -->
                                        <th rowspan="2"
                                            class="px-4 py-3 text-xs font-medium text-gray-600 dark:text-gray-300 uppercase text-center sticky right-0 z-20 bg-gray-100 dark:bg-gray-700 border-l border-gray-300 dark:border-gray-600">
                                            Actions
                                        </th>
                                    </tr>
                                    <tr>
                                        <!-- Case Information Subheaders -->
                                        <th
                                            class="px-3 py-3 text-xs font-medium text-gray-700 dark:text-gray-300 uppercase text-center border-r border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 whitespace-nowrap">
                                            Case #
                                        </th>
                                        <th
                                            class="px-3 py-3 text-xs font-medium text-gray-700 dark:text-gray-300 uppercase text-center border-r border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 whitespace-nowrap">
                                            Penalty Type
                                        </th>
                                        <th
                                            class="px-3 py-3 text-xs font-medium text-gray-700 dark:text-gray-300 uppercase text-center border-r border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 whitespace-nowrap">
                                            Date Issued
                                        </th>
                                        <th
                                            class="px-3 py-3 text-xs font-medium text-gray-700 dark:text-gray-300 uppercase text-center border-r border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 whitespace-nowrap">
                                            Status
                                        </th>
                                        <th
                                            class="px-3 py-3 text-xs font-medium text-gray-700 dark:text-gray-300 uppercase text-center border-r border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 whitespace-nowrap">
                                            Remarks
                                        </th>
                                        <!-- Response Status Subheaders -->
                                        <th
                                            class="px-3 py-3 text-xs font-medium text-gray-700 dark:text-gray-300 uppercase text-center border-r border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 whitespace-nowrap">
                                            Explanation Status
                                        </th>
                                        <th
                                            class="px-3 py-3 text-xs font-medium text-gray-700 dark:text-gray-300 uppercase text-center border-r border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 whitespace-nowrap">
                                            Uploaded File
                                        </th>
                                        <!-- Corrective Action Subheaders -->
                                        <th
                                            class="px-3 py-3 text-xs font-medium text-gray-700 dark:text-gray-300 uppercase text-center border-r border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 whitespace-nowrap">
                                            Action Type
                                        </th>
                                        <th
                                            class="px-3 py-3 text-xs font-medium text-gray-700 dark:text-gray-300 uppercase text-center border-r border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 whitespace-nowrap">
                                            Date Issued
                                        </th>
                                        <th
                                            class="px-3 py-3 text-xs font-medium text-gray-700 dark:text-gray-300 uppercase text-center border-r border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 whitespace-nowrap">
                                            Office Received
                                        </th>
                                        <th
                                            class="px-3 py-3 text-xs font-medium text-gray-700 dark:text-gray-300 uppercase text-center border-r border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 whitespace-nowrap">
                                            Employee Received
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($cases as $case)
                                        @php
                                            $isAttendanceCase =
                                                $case->notice &&
                                                in_array($case->notice->notice_type, ['AWOL', 'Tardiness']);
                                            $correctiveAction = $case->correctiveActions->first();
                                            $canUpload =
                                                !$case->answered_file_path &&
                                                $case->issued_date &&
                                                now()->diffInDays($case->issued_date) <= 5;
                                        @endphp
                                        <tr class="">
                                            <!-- Case Information -->
                                            <td
                                                class="px-3 py-4 text-center font-medium text-sm  border-gray-200 dark:border-gray-600 bg-gray-50/30 dark:bg-gray-600/20 whitespace-nowrap">
                                                <div class="font-semibold text-gray-900 dark:text-white">
                                                    {{ $case->admin_case_number ?? 'N/A' }}</div>
                                            </td>
                                            <td
                                                class="px-3 py-4 text-center  border-gray-200 dark:border-gray-600 bg-gray-50/30 dark:bg-gray-600/20 whitespace-nowrap">
                                                @if ($case->document)
                                                    <a href="#"
                                                        wire:click.prevent="downloadDocument({{ $case->document->id }})"
                                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                                        {{ $case->document->document_type }}
                                                    </a>
                                                @elseif($case->notice)
                                                    <a href="#"
                                                        wire:click.prevent="downloadNotice({{ $case->notice->id }})"
                                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                                        {{ $case->notice->notice_type }}
                                                    </a>
                                                @else
                                                    <span class="text-gray-500 dark:text-gray-400">N/A</span>
                                                @endif
                                            </td>
                                            <td
                                                class="px-3 py-4 text-center text-sm border-gray-200 dark:border-gray-600 bg-gray-50/30 dark:bg-gray-600/20 whitespace-nowrap">
                                                <div class="text-gray-900 dark:text-white">
                                                    {{ $case->created_at->format('M d, Y') }}</div>
                                            </td>
                                            <td
                                                class="px-3 py-4 text-center border-gray-200 dark:border-gray-600 bg-gray-50/30 dark:bg-gray-600/20 whitespace-nowrap">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $case->status === 'Resolved'
                                                        ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100'
                                                        : ($case->status === 'For Approval of the PCEO'
                                                            ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-100'
                                                            : ($case->status === 'Explanation Submitted'
                                                                ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-100'
                                                                : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100')) }}">
                                                    {{ $case->status }}
                                                </span>
                                            </td>
                                            <td
                                                class="px-3 py-4 text-center text-sm border-r border-gray-200 dark:border-gray-600 bg-gray-50/30 dark:bg-gray-600/20 whitespace-nowrap">
                                                <div class="text-gray-900 dark:text-white">{{ $case->remarks ?? 'N/A' }}
                                                </div>
                                            </td>
                                            <!-- Response Status -->
                                            <td
                                                class="px-3 py-4 text-center border-gray-200 dark:border-gray-600 bg-blue-50/30 dark:bg-blue-900/10 whitespace-nowrap">
                                                <div class="flex flex-col items-center space-y-1">
                                                    @if ($case->answered_file_path)
                                                        <span
                                                            class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100">
                                                            <i class="bi bi-check-circle mr-1"></i>
                                                            Submitted
                                                        </span>
                                                    @elseif($canUpload)
                                                        <span
                                                            class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100">
                                                            <i class="bi bi-clock mr-1"></i>
                                                            Pending ({{ 5 - now()->diffInDays($case->issued_date) }}
                                                            days left)
                                                        </span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100">
                                                            <i class="bi bi-x-circle mr-1"></i>
                                                            Expired
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td
                                                class="px-3 py-4 text-center border-r border-gray-200 dark:border-gray-600 bg-blue-50/30 dark:bg-blue-900/10 whitespace-nowrap">
                                                @if ($case->answered_file_path)
                                                    <div class="inline-flex flex-col items-center">
                                                        <button wire:click="downloadExplanation({{ $case->id }})"
                                                            class="inline-flex items-center px-2 py-1 text-xs bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-100 rounded hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors">
                                                            <i class="bi bi-file-earmark-pdf mr-1"></i>
                                                            explanation_{{ $case->admin_case_number }}.pdf
                                                        </button>
                                                        <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                            Uploaded:
                                                            {{ $case->answered_date ? date('M d, Y', strtotime($case->answered_date)) : 'N/A' }}
                                                        </span>
                                                    </div>
                                                @else
                                                    <span class="text-gray-400 dark:text-gray-500 text-sm">
                                                        No file uploaded
                                                    </span>
                                                @endif
                                            </td>
                                            <!-- Corrective Action -->
                                            <td
                                                class="px-3 py-4 text-center border-gray-200 dark:border-gray-600 bg-purple-50/30 dark:bg-purple-900/10 whitespace-nowrap">
                                                @if ($isAttendanceCase)
                                                    @if ($correctiveAction && $correctiveAction->pdf_path)
                                                        <a href="{{ route('download.corrective_action', $case->id) }}"
                                                            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                                            {{ $correctiveAction->action_taken_name ?? 'View Document' }}
                                                        </a>
                                                    @else
                                                        <span class="text-gray-500 dark:text-gray-400">
                                                            {{ $correctiveAction->action_taken_name ?? 'Pending' }}
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="text-gray-400 dark:text-gray-500">Not Applicable</span>
                                                @endif
                                            </td>
                                            <td
                                                class="px-3 py-4 text-center text-sm border-gray-200 dark:border-gray-600 bg-purple-50/30 dark:bg-purple-900/10 whitespace-nowrap">
                                                @if ($isAttendanceCase && $correctiveAction && $correctiveAction->date_issued)
                                                    <div class="text-gray-900 dark:text-white">
                                                        {{ $correctiveAction->date_issued->format('M d, Y') }}</div>
                                                @else
                                                    <span class="text-gray-400 dark:text-gray-500">
                                                        {{ $isAttendanceCase ? 'Pending' : 'N/A' }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td
                                                class="px-3 py-4 text-center text-sm border-gray-200 dark:border-gray-600 bg-purple-50/30 dark:bg-purple-900/10 whitespace-nowrap">
                                                @if ($isAttendanceCase && $correctiveAction && $correctiveAction->date_received_office)
                                                    <div class="text-gray-900 dark:text-white">
                                                        {{ $correctiveAction->date_received_office->format('M d, Y') }}
                                                    </div>
                                                @else
                                                    <span class="text-gray-400 dark:text-gray-500">
                                                        {{ $isAttendanceCase ? 'Pending' : 'N/A' }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td
                                                class="px-3 py-4 text-center border-gray-200 dark:border-gray-600 bg-purple-50/30 dark:bg-purple-900/10 whitespace-nowrap">
                                                @if ($isAttendanceCase && $correctiveAction && $correctiveAction->date_received_employee)
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100">
                                                        <i class="bi bi-check mr-1"></i>
                                                        {{ $correctiveAction->date_received_employee->format('M d, Y') }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400 dark:text-gray-500">
                                                        {{ $isAttendanceCase ? 'Pending' : 'N/A' }}
                                                    </span>
                                                @endif
                                            </td>
                                            <!-- Actions -->
                                            <td
                                                class="px-3 py-4 text-center sticky right-0 z-10 bg-white dark:bg-gray-800 border-l border-gray-300 dark:border-gray-600 whitespace-nowrap">
                                                <div class="flex justify-center items-center space-x-1">
                                                    <!-- Download Document/Notice -->
                                                    @if ($case->document)
                                                        <button
                                                            wire:click="downloadDocument({{ $case->document->id }})"
                                                            class="p-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                                                            title="Download Document">
                                                            <i class="bi bi-download text-sm"></i>
                                                        </button>
                                                    @endif
                                                    @if ($case->notice)
                                                        <button wire:click="downloadNotice({{ $case->notice->id }})"
                                                            class="p-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                                                            title="Download Notice">
                                                            <i class="bi bi-download text-sm"></i>
                                                        </button>
                                                    @endif
                                                    <!-- Upload Explanation -->
                                                    @if ($canUpload)
                                                        <button wire:click="setCurrentCase({{ $case->id }})"
                                                            class="p-2 text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-colors"
                                                            title="Upload Explanation">
                                                            <i class="bi bi-upload text-sm"></i>
                                                        </button>
                                                    @endif
                                                    <!-- Download Explanation -->
                                                    @if ($case->answered_file_path)
                                                        <button wire:click="downloadExplanation({{ $case->id }})"
                                                            class="p-2 text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-300 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-lg transition-colors"
                                                            title="Download Explanation">
                                                            <i class="bi bi-file-earmark-text text-sm"></i>
                                                        </button>
                                                    @endif
                                                    <!-- Download Corrective Action -->
                                                    @if ($isAttendanceCase && $correctiveAction && $correctiveAction->pdf_path)
                                                        <a href="{{ route('download.corrective_action', $case->id) }}"
                                                            class="p-2 text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition-colors"
                                                            title="Download Corrective Action">
                                                            <i class="bi bi-file-earmark-pdf text-sm"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11"
                                                class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                                <div class="flex flex-col items-center">
                                                    <i class="bi bi-inbox text-4xl mb-2"></i>
                                                    <p class="text-lg font-medium">No administrative cases found</p>
                                                    <p class="text-sm">You don't have any administrative cases at the
                                                        moment.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            {{ $cases->links() }}
        </div>
    </div>

    <!-- Upload Explanation Modal -->
    <x-modal wire:model="showUploadModal" max-width="lg">
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-3">
                    <div
                        class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                        <i class="bi bi-upload text-blue-600 dark:text-blue-400 text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Upload Explanation Document
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Submit your written explanation for this administrative case
                        </p>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6">
                <!-- File Upload Area -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Select PDF Document
                    </label>

                    <div class="relative">
                        <input type="file" wire:model="uploadFile" id="fileUpload" accept=".pdf"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                        <div
                            class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center hover:border-blue-400 dark:hover:border-blue-500 transition-colors">
                            <div class="flex flex-col items-center">
                                <i class="bi bi-cloud-upload text-4xl text-gray-400 dark:text-gray-500 mb-3"></i>
                                <p class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Choose a file or drag it here
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    PDF only, maximum 10MB
                                </p>

                                <!-- Selected file display -->
                                @if ($uploadFile)
                                    <div
                                        class="mt-4 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                                        <div class="flex items-center space-x-2">
                                            <i class="bi bi-file-earmark-pdf text-green-600 dark:text-green-400"></i>
                                            <span class="text-sm font-medium text-green-700 dark:text-green-300">
                                                {{ $uploadFile->getClientOriginalName() }}
                                            </span>
                                            <span class="text-xs text-green-600 dark:text-green-400">
                                                ({{ number_format($uploadFile->getSize() / 1024, 2) }} KB)
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @error('uploadFile')
                        <div class="mt-2 flex items-center space-x-2 text-red-600 dark:text-red-400">
                            <i class="bi bi-exclamation-circle text-sm"></i>
                            <span class="text-sm">{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <!-- Important Notice -->
                <div
                    class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4 mb-6">
                    <div class="flex items-start space-x-3">
                        <i class="bi bi-info-circle text-amber-600 dark:text-amber-400 text-lg mt-0.5"></i>
                        <div>
                            <h4 class="text-sm font-medium text-amber-800 dark:text-amber-300 mb-1">
                                Important Reminder
                            </h4>
                            <p class="text-sm text-amber-700 dark:text-amber-400">
                                Please ensure your explanation is comprehensive and addresses all points mentioned in
                                the administrative case. Once uploaded, you cannot modify the document.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-end space-x-3 px-6 py-4 bg-white dark:bg-gray-800 rounded-b-lg">
                <button type="button" wire:click="$set('showUploadModal', false)"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors">
                    Cancel
                </button>

                <button wire:click="uploadExplanation" wire:loading.attr="disabled"
                    wire:loading.class="opacity-50 cursor-not-allowed"
                    class="px-6 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 rounded-md transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2">

                    <span wire:loading.remove wire:target="uploadExplanation">
                        <i class="bi bi-upload mr-2"></i>
                        Upload Document
                    </span>

                    <span wire:loading wire:target="uploadExplanation" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Uploading...
                    </span>
                </button>
            </div>
        </div>
    </x-modal>
</div>

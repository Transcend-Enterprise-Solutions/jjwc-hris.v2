<div class="w-full overflow-hidden relative"
x-data="{
    showAddEdit: true,
}" x-cloak>

    <div class="flex justify-between flex-wrap bg-gray-100 dark:bg-gray-700 px-6 py-4">
        <h2 class="text-xl font-bold dark:text-white">Other Information</h2>

        @if(!$editQuestions)
            <div class="flex gap-2">
                <button wire:click="editC4Question" @click="showAddEdit = false"
                        x-show="showAddEdit === true"
                        class="inline-flex items-center px-4 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                </button>
            </div>
        @endif

        @if($editQuestions)
            <div class="flex gap-2">
                <button wire:click="saveC4Question" 
                        @click="showAddEdit = true"
                        class="inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-md shadow-sm transition-colors duration-200">
                    Save
                </button>
                <button wire:click="resetVariables" @click="showAddEdit = true"
                        class="inline-flex items-center px-3 py-1 bg-gray-600 hover:bg-gray-700 text-white text-xs font-medium rounded-md shadow-sm transition-colors duration-200">
                    Cancel
                </button>
            </div>
        @endif
    </div>

    <div class="m-scrollable p-6 rounded-lg shadow-sm overflow-hidden">
        
        {{-- Question 34 --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
            <div class="bg-gray-100 dark:bg-gray-700 px-6 py-4">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                    34. Are you related by consanguinity or affinity to the appointing or recommending authority, or to the chief of bureau or office or to the person who has immediate supervision over you in the Office, Bureau or Department where you will be appointed?
                </h3>
            </div>
            
            {{-- Question 34a --}}
            <div class="border-b border-gray-200 dark:border-gray-700">
                <div class="p-6 {{ $editAnswer['q34a'] ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                    <div class="flex justify-between items-start gap-4">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                                a. Within the third degree?
                            </p>
                            
                            <div class="flex items-center gap-6">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" value="1" wire:model="q34aAnswer" name="answer34a"
                                           class="w-4 h-4 text-blue-600 focus:ring-2 focus:ring-blue-500"
                                           {{ $editAnswer['q34a'] ? '' : 'disabled' }}>
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Yes</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" value="0" wire:model="q34aAnswer" name="answer34a"
                                           class="w-4 h-4 text-blue-600 focus:ring-2 focus:ring-blue-500"
                                           {{ $editAnswer['q34a'] ? '' : 'disabled' }}>
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">No</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Question 34b --}}
            <div class="p-6 {{ $editAnswer['q34b'] ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                <div class="flex justify-between items-start gap-4">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                            b. Within the fourth degree (for Local Government Unit - Career Employees)?
                        </p>
                        
                        <div class="flex items-center gap-6 mb-4">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" value="1" wire:model.live="q34bAnswer" name="answer34b"
                                       class="w-4 h-4 text-blue-600 focus:ring-2 focus:ring-blue-500"
                                       {{ $editAnswer['q34b'] ? '' : 'disabled' }}>
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Yes</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" value="0" wire:model.live="q34bAnswer" name="answer34b"
                                       class="w-4 h-4 text-blue-600 focus:ring-2 focus:ring-blue-500"
                                       {{ $editAnswer['q34b'] ? '' : 'disabled' }}>
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">No</span>
                            </label>
                        </div>
                        
                        @if($q34bAnswer || $editAnswer['q34b'])
                            <div class="form-group">
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">
                                    If YES, give details:
                                </label>
                                @if($editAnswer['q34b'])
                                    <input wire:model="q34bDetails" type="text"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           placeholder="Enter details">
                                    @error('q34bDetails')
                                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                @else
                                    <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md">
                                        <span class="text-sm text-gray-900 dark:text-gray-100">{{ $q34bDetails ?: '--' }}</span>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Question 35 --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
            <div class="bg-gray-100 dark:bg-gray-700 px-6 py-4">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">35. Administrative & Criminal Offenses</h3>
            </div>
            
            {{-- Question 35a --}}
            <div class="border-b border-gray-200 dark:border-gray-700">
                <div class="p-6 {{ $editAnswer['q35a'] ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                    <div class="flex justify-between items-start gap-4">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                                a. Have you ever been found guilty of any administrative offense?
                            </p>
                            
                            <div class="flex items-center gap-6 mb-4">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" value="1" wire:model.live="q35aAnswer" name="answer35a"
                                           class="w-4 h-4 text-blue-600 focus:ring-2 focus:ring-blue-500"
                                           {{ $editAnswer['q35a'] ? '' : 'disabled' }}>
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Yes</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" value="0" wire:model.live="q35aAnswer" name="answer35a"
                                           class="w-4 h-4 text-blue-600 focus:ring-2 focus:ring-blue-500"
                                           {{ $editAnswer['q35a'] ? '' : 'disabled' }}>
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">No</span>
                                </label>
                            </div>
                            
                            @if($q35aAnswer || $editAnswer['q35a'])
                                <div class="form-group">
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">
                                        If YES, give details:
                                    </label>
                                    @if($editAnswer['q35a'])
                                        <input wire:model="q35aDetails" type="text"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                               placeholder="Enter details">
                                        @error('q35aDetails')
                                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    @else
                                        <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md">
                                            <span class="text-sm text-gray-900 dark:text-gray-100">{{ $q35aDetails ?: '--' }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Question 35b --}}
            <div class="p-6 {{ $editAnswer['q35b'] ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                <div class="flex justify-between items-start gap-4">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                            b. Have you been criminally charged before any court?
                        </p>
                        
                        <div class="flex items-center gap-6 mb-4">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" value="1" wire:model.live="q35bAnswer" name="answer35b"
                                       class="w-4 h-4 text-blue-600 focus:ring-2 focus:ring-blue-500"
                                       {{ $editAnswer['q35b'] ? '' : 'disabled' }}>
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Yes</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" value="0" wire:model.live="q35bAnswer" name="answer35b"
                                       class="w-4 h-4 text-blue-600 focus:ring-2 focus:ring-blue-500"
                                       {{ $editAnswer['q35b'] ? '' : 'disabled' }}>
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">No</span>
                            </label>
                        </div>
                        
                        @if($q35bAnswer || $editAnswer['q35b'])
                            <div class="space-y-4">
                                <div class="form-group">
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">
                                        Date Filed:
                                    </label>
                                    @if($editAnswer['q35b'])
                                        <input wire:model="q35bDate_filed" type="date"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        @error('q35bDate_filed')
                                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    @else
                                        <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md">
                                            <span class="text-sm text-gray-900 dark:text-gray-100">{{ $q35bDate_filed ?: '--' }}</span>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="form-group">
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">
                                        Status of Case/s:
                                    </label>
                                    @if($editAnswer['q35b'])
                                        <input wire:model="q35bStatus" type="text"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                               placeholder="Enter case status">
                                        @error('q35bStatus')
                                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    @else
                                        <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md">
                                            <span class="text-sm text-gray-900 dark:text-gray-100">{{ $q35bStatus ?: '--' }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Question 36 --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
            <div class="p-6 {{ $editAnswer['q36a'] ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                <div class="flex justify-between items-start gap-4">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white mb-4">
                            36. Have you ever been convicted of any crime or violation of any law, decree, ordinance or regulation by any court or tribunal?
                        </p>
                        
                        <div class="flex items-center gap-6 mb-4">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" value="1" wire:model.live="q36aAnswer" name="answer36a"
                                       class="w-4 h-4 text-blue-600 focus:ring-2 focus:ring-blue-500"
                                       {{ $editAnswer['q36a'] ? '' : 'disabled' }}>
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Yes</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" value="0" wire:model.live="q36aAnswer" name="answer36a"
                                       class="w-4 h-4 text-blue-600 focus:ring-2 focus:ring-blue-500"
                                       {{ $editAnswer['q36a'] ? '' : 'disabled' }}>
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">No</span>
                            </label>
                        </div>
                        
                        @if($q36aAnswer || $editAnswer['q36a'])
                            <div class="form-group">
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">
                                    If YES, give details:
                                </label>
                                @if($editAnswer['q36a'])
                                    <input wire:model="q36aDetails" type="text"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           placeholder="Enter details">
                                    @error('q36aDetails')
                                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                @else
                                    <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md">
                                        <span class="text-sm text-gray-900 dark:text-gray-100">{{ $q36aDetails ?: '--' }}</span>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Question 37 --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
            <div class="p-6 {{ $editAnswer['q37a'] ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                <div class="flex justify-between items-start gap-4">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white mb-4">
                            37. Have you ever been separated from the service in any of the following modes: resignation, retirement, dropped from the rolls, dismissal, termination, end of term, finished contract or phased out (abolition) in the public or private sector?
                        </p>
                        
                        <div class="flex items-center gap-6 mb-4">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" value="1" wire:model.live="q37aAnswer" name="answer37a"
                                       class="w-4 h-4 text-blue-600 focus:ring-2 focus:ring-blue-500"
                                       {{ $editAnswer['q37a'] ? '' : 'disabled' }}>
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Yes</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" value="0" wire:model.live="q37aAnswer" name="answer37a"
                                       class="w-4 h-4 text-blue-600 focus:ring-2 focus:ring-blue-500"
                                       {{ $editAnswer['q37a'] ? '' : 'disabled' }}>
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">No</span>
                            </label>
                        </div>
                        
                        @if($q37aAnswer || $editAnswer['q37a'])
                            <div class="form-group">
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">
                                    If YES, give details:
                                </label>
                                @if($editAnswer['q37a'])
                                    <input wire:model="q37aDetails" type="text"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           placeholder="Enter details">
                                    @error('q37aDetails')
                                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                @else
                                    <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md">
                                        <span class="text-sm text-gray-900 dark:text-gray-100">{{ $q37aDetails ?: '--' }}</span>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Question 38 --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
            <div class="bg-gray-100 dark:bg-gray-700 px-6 py-4">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">38. Election Candidacy</h3>
            </div>
            
            {{-- Question 38a --}}
            <div class="border-b border-gray-200 dark:border-gray-700">
                <div class="p-6 {{ $editAnswer['q38a'] ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                    <div class="flex justify-between items-start gap-4">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                                a. Have you ever been a candidate in a national or local election held within the last year (except Barangay election)?
                            </p>
                            
                            <div class="flex items-center gap-6 mb-4">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" value="1" wire:model.live="q38aAnswer" name="answer38a"
                                           class="w-4 h-4 text-blue-600 focus:ring-2 focus:ring-blue-500"
                                           {{ $editAnswer['q38a'] ? '' : 'disabled' }}>
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Yes</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" value="0" wire:model.live="q38aAnswer" name="answer38a"
                                           class="w-4 h-4 text-blue-600 focus:ring-2 focus:ring-blue-500"
                                           {{ $editAnswer['q38a'] ? '' : 'disabled' }}>
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">No</span>
                                </label>
                            </div>
                            
                            @if($q38aAnswer || $editAnswer['q38a'])
                                <div class="form-group">
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">
                                        If YES, give details:
                                    </label>
                                    @if($editAnswer['q38a'])
                                        <input wire:model="q38aDetails" type="text"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                               placeholder="Enter details">
                                        @error('q38aDetails')
                                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    @else
                                        <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md">
                                            <span class="text-sm text-gray-900 dark:text-gray-100">{{ $q38aDetails ?: '--' }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Question 38b --}}
            <div class="p-6 {{ $editAnswer['q38b'] ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                <div class="flex justify-between items-start gap-4">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                            b. Have you resigned from the government service during the three (3)-month period before the last election to promote/actively campaign for a national or local candidate?
                        </p>
                        
                        <div class="flex items-center gap-6 mb-4">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" value="1" wire:model.live="q38bAnswer" name="answer38b"
                                       class="w-4 h-4 text-blue-600 focus:ring-2 focus:ring-blue-500"
                                       {{ $editAnswer['q38b'] ? '' : 'disabled' }}>
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Yes</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" value="0" wire:model.live="q38bAnswer" name="answer38b"
                                       class="w-4 h-4 text-blue-600 focus:ring-2 focus:ring-blue-500"
                                       {{ $editAnswer['q38b'] ? '' : 'disabled' }}>
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">No</span>
                            </label>
                        </div>
                        
                        @if($q38bAnswer || $editAnswer['q38b'])
                            <div class="form-group">
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">
                                    If YES, give details:
                                </label>
                                @if($editAnswer['q38b'])
                                    <input wire:model="q38bDetails" type="text"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           placeholder="Enter details">
                                    @error('q38bDetails')
                                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                @else
                                    <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md">
                                        <span class="text-sm text-gray-900 dark:text-gray-100">{{ $q38bDetails ?: '--' }}</span>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Question 39 --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
            <div class="p-6 {{ $editAnswer['q39a'] ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                <div class="flex justify-between items-start gap-4">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white mb-4">
                            39. Have you acquired the status of an immigrant or permanent resident of another country?
                        </p>
                        
                        <div class="flex items-center gap-6 mb-4">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" value="1" wire:model.live="q39aAnswer" name="answer39a"
                                       class="w-4 h-4 text-blue-600 focus:ring-2 focus:ring-blue-500"
                                       {{ $editAnswer['q39a'] ? '' : 'disabled' }}>
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Yes</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" value="0" wire:model.live="q39aAnswer" name="answer39a"
                                       class="w-4 h-4 text-blue-600 focus:ring-2 focus:ring-blue-500"
                                       {{ $editAnswer['q39a'] ? '' : 'disabled' }}>
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">No</span>
                            </label>
                        </div>
                        
                        @if($q39aAnswer || $editAnswer['q39a'])
                            <div class="form-group">
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">
                                    If YES, give details (country):
                                </label>
                                @if($editAnswer['q39a'])
                                    <input wire:model="q39aDetails" type="text"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           placeholder="Enter country">
                                    @error('q39aDetails')
                                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                @else
                                    <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md">
                                        <span class="text-sm text-gray-900 dark:text-gray-100">{{ $q39aDetails ?: '--' }}</span>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Question 40 --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
            <div class="bg-gray-100 dark:bg-gray-700 px-6 py-4">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                    40. Pursuant to: (a) Indigenous People's Act (RA 8371); (b) Magna Carta for Disabled Persons (RA 7277, as amended); and (c) Solo Parents Welfare Act of 2000 (RA 8972)
                </h3>
            </div>
            
            {{-- Question 40a --}}
            <div class="border-b border-gray-200 dark:border-gray-700">
                <div class="p-6 {{ $editAnswer['q40a'] ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                    <div class="flex justify-between items-start gap-4">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                                a. Are you a member of any indigenous group?
                            </p>
                            
                            <div class="flex items-center gap-6 mb-4">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" value="1" wire:model.live="q40aAnswer" name="answer40a"
                                           class="w-4 h-4 text-blue-600 focus:ring-2 focus:ring-blue-500"
                                           {{ $editAnswer['q40a'] ? '' : 'disabled' }}>
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Yes</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" value="0" wire:model.live="q40aAnswer" name="answer40a"
                                           class="w-4 h-4 text-blue-600 focus:ring-2 focus:ring-blue-500"
                                           {{ $editAnswer['q40a'] ? '' : 'disabled' }}>
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">No</span>
                                </label>
                            </div>
                            
                            @if($q40aAnswer || $editAnswer['q40a'])
                                <div class="form-group">
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">
                                        If YES, please specify:
                                    </label>
                                    @if($editAnswer['q40a'])
                                        <input wire:model="q40aDetails" type="text"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                               placeholder="Enter indigenous group">
                                        @error('q40aDetails')
                                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    @else
                                        <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md">
                                            <span class="text-sm text-gray-900 dark:text-gray-100">{{ $q40aDetails ?: '--' }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Question 40b --}}
            <div class="border-b border-gray-200 dark:border-gray-700">
                <div class="p-6 {{ $editAnswer['q40b'] ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                    <div class="flex justify-between items-start gap-4">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                                b. Are you a person with disability?
                            </p>
                            
                            <div class="flex items-center gap-6 mb-4">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" value="1" wire:model.live="q40bAnswer" name="answer40b"
                                           class="w-4 h-4 text-blue-600 focus:ring-2 focus:ring-blue-500"
                                           {{ $editAnswer['q40b'] ? '' : 'disabled' }}>
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Yes</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" value="0" wire:model.live="q40bAnswer" name="answer40b"
                                           class="w-4 h-4 text-blue-600 focus:ring-2 focus:ring-blue-500"
                                           {{ $editAnswer['q40b'] ? '' : 'disabled' }}>
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">No</span>
                                </label>
                            </div>
                            
                            @if($q40bAnswer || $editAnswer['q40b'])
                                <div class="form-group">
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">
                                        If YES, please specify ID No:
                                    </label>
                                    @if($editAnswer['q40b'])
                                        <input wire:model="q40bDetails" type="text"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                               placeholder="Enter PWD ID number">
                                        @error('q40bDetails')
                                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    @else
                                        <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md">
                                            <span class="text-sm text-gray-900 dark:text-gray-100">{{ $q40bDetails ?: '--' }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Question 40c --}}
            <div class="p-6 {{ $editAnswer['q40c'] ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                <div class="flex justify-between items-start gap-4">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                            c. Are you a solo parent?
                        </p>
                        
                        <div class="flex items-center gap-6 mb-4">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" value="1" wire:model.live="q40cAnswer" name="answer40c"
                                       class="w-4 h-4 text-blue-600 focus:ring-2 focus:ring-blue-500"
                                       {{ $editAnswer['q40c'] ? '' : 'disabled' }}>
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Yes</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" value="0" wire:model.live="q40cAnswer" name="answer40c"
                                       class="w-4 h-4 text-blue-600 focus:ring-2 focus:ring-blue-500"
                                       {{ $editAnswer['q40c'] ? '' : 'disabled' }}>
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">No</span>
                            </label>
                        </div>
                        
                        @if($q40cAnswer || $editAnswer['q40c'])
                            <div class="form-group">
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">
                                    If YES, please specify ID No:
                                </label>
                                @if($editAnswer['q40c'])
                                    <input wire:model="q40cDetails" type="text"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           placeholder="Enter Solo Parent ID number">
                                    @error('q40cDetails')
                                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                @else
                                    <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md">
                                        <span class="text-sm text-gray-900 dark:text-gray-100">{{ $q40cDetails ?: '--' }}</span>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

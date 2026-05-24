<div class="w-full">
    
    <style>
        @-webkit-keyframes spinner-border {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes spinner-border {
            to {
                transform: rotate(360deg);
            }
        }

        .spinner-border {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            vertical-align: text-bottom;
            border: 2px solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            -webkit-animation: spinner-border .75s linear infinite;
            animation: spinner-border .75s linear infinite;
            color: rgb(0, 255, 42);
        }
    </style>

    <div class="flex flex-col justify-center w-full">
        @if($recordId)
            <div class="w-full bg-white rounded-2xl p-3 sm:p-6 shadow dark:bg-gray-800 overflow-x-visible">
                <div class="overflow-hidden rounded-lg">
                    <div class="p-2 w-full overflow-x-auto rounded-lg bg-gray-200">
                        <p class="text-gray-800 text-md mb-2 mt-4">
                            Add/Edit Service Record for: <span class="text-black font-bold">{{ $name }}</span>
                        </p>
                        
                        <table class="w-full border-collapse border border-gray-800">
                            <thead>
                                <tr class="bg-green-700">
                                    @foreach ($headers as $header)
                                        <th class="border border-gray-200 px-4 py-1 text-xs text-white">{{ $header }}</th>
                                    @endforeach
                                    <th class="border border-gray-200 px-4 py-1 text-xs text-white">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tableData as $index => $row)
                                    <tr>
                                        @foreach ($row as $colIndex => $cell)
                                            <td class="border border-gray-500 align-top bg-gray-100 {{ $colIndex == 0 || $colIndex == 'is_new' ? 'hidden' : '' }}">
                                                @if($editingRow === $index)
                                                    <div class="flex flex-col">
                                                        <textarea 
                                                            wire:model="tableData.{{ $index }}.{{ $colIndex }}"
                                                            x-data="{ resize() { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'; } }"
                                                            x-init="resize()" 
                                                            @input="resize()"
                                                            class="w-full text-center text-gray-800 text-xs px-2 py-1 border-none focus:ring-0 
                                                            whitespace-normal break-words resize-none overflow-hidden min-h-[20px]
                                                            @error('tableData.'.$index.'.'.$colIndex) border border-red-500 @enderror"
                                                        ></textarea>
                                                        @error('tableData.'.$index.'.'.$colIndex)
                                                            <span class="text-red-500 text-xs mt-1 text-center">Add a valid data</span>
                                                        @enderror
                                                    </div>
                                                @else
                                                    <div class="bg-gray-100 w-full text-center text-gray-800 text-xs px-2 py-1 whitespace-normal break-words">
                                                        {{ $cell }}
                                                    </div>
                                                @endif
                                            </td>
                                        @endforeach
                                        <td class="border border-gray-500 bg-gray-100 text-center align-middle">
                                            @if($editingRow === $index)
                                                <!-- Save and Cancel buttons for any row being edited -->
                                                <button 
                                                    wire:click="saveRecords({{ $index }})"
                                                    class="p-1 text-xs text-white bg-green-500 rounded hover:bg-green-600 mr-1"
                                                    title="Save"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </button>
                                                <button 
                                                    wire:click="cancelEdit" 
                                                    class="p-1 text-xs text-white bg-red-500 rounded hover:bg-red-600" 
                                                    title="Cancel"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            @else
                                                <!-- For rows not being edited -->
                                                @if(isset($row['is_new']) || (isset($row[0]) && $row[0]))
                                                    <button 
                                                        wire:click="editRow({{ $index }})"
                                                        class="p-1 text-xs text-white bg-blue-500 rounded hover:bg-blue-600 mr-1"
                                                        title="Edit"
                                                    >
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </button>
                                                @endif
                                                
                                                @if(isset($row[0]) && $row[0]) {{-- Only show delete button for existing records --}}
                                                    <button 
                                                        wire:click="deleteRow({{ $row[0] }})"
                                                        class="p-1 text-xs text-white bg-red-500 rounded hover:bg-red-600"
                                                        title="Delete"
                                                    >
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        @if (empty($tableData))
                            <div class="p-4 border border-gray-500 text-center text-gray-600">
                                No records!
                            </div> 
                        @endif
                    </div>
                </div>
                
                <div class="flex space-x-4 mt-4">
                    <button 
                        wire:click="addRow"
                        class="text-sm px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600"
                    >
                        Add Record
                    </button>
                    <button 
                        wire:click="resetVariables"
                        class="text-sm px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        @else
            <div class="w-full bg-white rounded-2xl p-3 sm:p-6 shadow dark:bg-gray-800 overflow-x-visible">

                <div class="pb-4 mb-3 pt-4 sm:pt-0">
                    <h1 class="text-lg font-bold text-center text-slate-800 dark:text-white">Service Records</h1>
                </div>

                <div class="mb-6 flex flex-col sm:flex-row items-end justify-between">
                    
                    <div class="w-full sm:w-1/3 sm:mr-4">
                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Search</label>
                        <input type="text" id="search" wire:model.live="search"
                            class="px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-400 hover:bg-gray-300 rounded-md
                                dark:hover:bg-slate-600 dark:border-slate-600
                                dark:text-gray-300 dark:bg-gray-800"
                            placeholder="Enter employee name or ID">
                    </div>

                    <div class="w-full sm:w-auto">
                        <button wire:click="toggleEditSig" 
                            class="text-sm mt-4 sm:mt-1 px-2 py-1.5 bg-green-500 text-white rounded-md 
                            hover:bg-green-600 focus:outline-none dark:bg-gray-700 w-full
                            dark:hover:bg-green-600 dark:text-gray-300 dark:hover:text-white">
                            Edit Signatory
                        </button>
                    </div>

                </div>


                <!-- Table -->
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto">
                        <div class="inline-block w-full py-2 align-middle">
                            <div class="overflow-hidden border dark:border-gray-700 rounded-lg">
                                <div class="overflow-x-auto">
                                    <table class="w-full min-w-full">
                                        <thead class="bg-gray-200 dark:bg-gray-700 rounded-xl">
                                            <tr class="whitespace-nowrap">
                                                <th scope="col"
                                                    class="px-5 py-3 text-sm font-medium text-left uppercase">
                                                    Employee
                                                </th>
                                                <th scope="col"
                                                    class="px-5 py-3 text-sm font-medium text-center uppercase">
                                                    Employee Number
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-sm font-medium uppercase text-center">
                                                    Years in Government Service
                                                </th>
                                                <th class="px-5 py-3 text-gray-100 text-sm font-medium text-right sticky right-0 z-10 bg-gray-600 dark:bg-gray-600">
                                                    Action
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($users as $user)
                                                <tr class="text-sm whitespace-nowrap border-b border-neutral-200 dark:border-gray-700">
                                                    <td class="px-4 py-2 text-left">
                                                        <div class="flex gap-3 items-center">
                                                            @if ($user->profile_photo_path)
                                                                <img src="{{ route('profile-photo.file', ['filename' => basename($user->profile_photo_path)]) }}"
                                                                    alt="{{ $user?->name ?? 'No User Assigned' }}"
                                                                    width="32" height="32"
                                                                    class="w-10 h-10 rounded-full object-cover border border-gray-500">
                                                            @else
                                                                <div class="w-10 h-10 rounded-full bg-gray-500 border border-gray-500 dark:bg-gray-600 flex items-center justify-center text-white text-xs font-medium">
                                                                    {{ strtoupper(substr(($user?->name ?? 'No User Assigned'), 0, 1)) }}{{ strtoupper(substr(explode(' ', ($user?->name ?? 'No User Assigned'))[1] ?? '', 0, 1)) }}
                                                                </div>
                                                            @endif
                                                            <span>{{ $user->surname }}, {{ $user->first_name }}{{ $user->middle_name && $user->middle_name !== 'N/A' && $user->middle_name !== 'n/a' ? ' ' . $user->middle_name : '' }}{{ ($user->name_extension && $user->name_extension !== 'N/A' && $user->name_extension !== 'n/a') ? ' ' . $user->name_extension : '' }}</span>
                                                        </div>                    
                                                    </td>
                                                    <td class="px-4 py-2 text-center">
                                                        {{ $user->emp_code }}
                                                    </td>
                                                    <td class="px-4 py-2 text-center">
                                                        {{ $user->formatted_gov_service }}
                                                    </td>
                                                    <td class="px-5 py-4 text-sm font-medium text-right
                                                        border-b border-neutral-200 dark:border-gray-700 
                                                        whitespace-nowrap sticky right-0 z-10">
                                                        <div class="bg-white dark:bg-gray-800 w-full h-full">
                                                            <button wire:click="toggleViewRecord({{ $user->id }})"
                                                                class="inline-flex items-center justify-center px-4 py-2 -m-5 -mr-2 text-sm font-medium tracking-wide text-blue-500 hover:text-blue-600 focus:outline-none">
                                                                <i class="bi bi-pencil-fill" title="Edit Service Record"></i>
                                                            </button>
                                                            <div class="relative mt-2" style="margin-right: -2px;">
                                                                <button
                                                                    wire:click.prevent="exportRecord({{ $user->id }})"
                                                                    class="peer inline-flex items-center justify-center px-4 py-2 -m-5 -mr-2
                                                                    text-sm font-medium tracking-wide text-green-500 hover:text-green-600 focus:outline-none"
                                                                    title="Export Service Record" wire:target="exportRecord({{ $user->id }})"
                                                                    wire:loading.remove>
                                                                    <img class="flex dark:hidden ml-3"
                                                                        src="/images/icons8-xls-export-dark.png"
                                                                        width="18" height="18" alt="">
                                                                    <img class="hidden dark:block ml-3"
                                                                        src="/images/icons8-xls-export-light.png"
                                                                        width="18" height="18" alt="">
                                                                </button>
                                                                <div wire:loading  class="w-full flex justify-end items-center" style="padding-right: 10px; margin-top: -5px"
                                                                    wire:target="exportRecord({{ $user->id }})">
                                                                    <div class="spinner-border small text-primary"
                                                                        role="status">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @if ($users->isEmpty())
                                        <div class="p-4 text-center text-gray-500 dark:text-gray-300">
                                            No records!
                                        </div> 
                                    @endif
                                </div>
                                <div class="p-5 text-neutral-500 dark:text-neutral-200 bg-gray-200 dark:bg-gray-700">
                                    {{ $users->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        @endif
    </div>



    {{-- Add Signatory Modal --}}
    <x-modal id="addSignatory" maxWidth="2xl" wire:model="editSig" centered>
        <div class="p-4">
            <div class="flex items-center justify-between pb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">
                    Edit Signatory
                </h3>
                <button @click="show = false" class="float-right focus:outline-none" wire:click='resetVariables'>
                    <i class="fas fa-times"></i>
                </button>
            </div>
            {{-- Form fields --}}
            <form wire:submit.prevent='saveSignatory'>
                <div class="grid grid-cols-2 gap-4">
                    
                    <div class="col-span-2">
                        <label for="userId" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Signatory 1</label>
                        <select id="userId" wire:model='userId' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                            <option value="{{ $userId }}">{{ $name ? $name : 'Select an employee' }}</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                        @error('userId') 
                            <span class="text-red-500 text-sm">Please select an employee!</span> 
                        @enderror
                    </div>
                    
                    <div class="col-span-2">
                        <label for="userId2" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Signatory 2</label>
                        <select id="userId2" wire:model='userId2' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                            <option value="{{ $userId2 }}">{{ $name2 ? $name2 : 'Select an employee' }}</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                        @error('userId2') 
                            <span class="text-red-500 text-sm">Please select an employee!</span> 
                        @enderror
                    </div>
                    
                    {{-- Save and Cancel buttons --}}
                    <div class="mt-4 flex justify-end col-span-2">
                        <button class="mr-2 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            <div wire:loading wire:target="saveSignatory" style="margin-right: 5px">
                                <div class="spinner-border small text-primary" role="status">
                                </div>
                            </div>
                            Save
                        </button>
                        <p @click="show = false" class="bg-gray-400 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded cursor-pointer" wire:click='resetVariables'>
                            Cancel
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>

    {{-- Delete Modal --}}
    <x-modal id="deleteModal" maxWidth="md" wire:model="toDeleteId" centered>
        <div class="p-4">
            <div class="mb-4 text-slate-900 dark:text-gray-100 font-bold">
                Confirm Deletion
                <button @click="show = false" class="float-right focus:outline-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">
                Are you sure you want to delete this record?
            </label>

            <form wire:submit.prevent='deleteRecord'>
                <div class="mt-4 flex justify-end col-span-1 sm:col-span-1">
                    <button class="mr-2 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        <div wire:loading wire:target="deleteData" style="margin-bottom: 5px;">
                            <div class="spinner-border small text-primary" role="status">
                            </div>
                        </div>
                        Delete
                    </button>
                    <p @click="show = false"
                        class="bg-gray-400 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded cursor-pointer">
                        Cancel
                    </p>
                </div>
            </form>

        </div>
    </x-modal>

</div>
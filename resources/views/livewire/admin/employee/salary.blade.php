<div class="w-full">
    
    <div class="w-full flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex items-center">
            <i class="bi bi-briefcase-fill mr-2 text-blue-600"></i>
            Salary Details
        </h3>


        <button wire:click="toggleEditSalary({{ $selectedUser->id }})"
                class="px-2 py-1.5 bg-blue-500 text-white rounded-md text-xs
                hover:bg-blue-600 focus:outline-none dark:bg-gray-700
                dark:hover:bg-blue-600 dark:text-gray-300 dark:hover:text-white">
            <i class="bi {{ $selectedUser->salary ? 'bi-pencil' : 'bi-plus-lg' }} mr-2"></i> {{ $selectedUser->salary ? 'Edit' : 'Add' }} Salary
        </button>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-3">
        <div class="col-span-full flex items-start justify-start   gap-2 text-xs">
            <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Salary Grade:</span>
            <span class="text-gray-900 dark:text-gray-100">
                {{ $selectedUser->salary && $selectedUser->salary->sg ? $selectedUser->salary->sg . ($selectedUser->salary->step && $selectedUser->salary->step != 1 ? '-' . $selectedUser->salary->step : '' ) : '--'  }}
            </span>
        </div>
        <div class="col-span-full flex items-start justify-start   gap-2 text-xs">
            <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Basic Salary:</span>
            <span class="text-gray-900 dark:text-gray-100">
                {{ $selectedUser->salary ? '₱' . number_format($selectedUser->salary->monthly_basic_salary, 2, '.', ',') : '--'  }}
            </span>
        </div>
        <div class="col-span-full flex items-start justify-start   gap-2 text-xs">
            <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">PERA:</span>
            <span class="text-gray-900 dark:text-gray-100">
                {{ $selectedUser->salary ? '₱' . number_format($selectedUser->salary->pera, 2, '.', ',') : '--'  }}
            </span>
        </div>
    </div>


    {{-- Add and Edit Salary Modal --}}
    <x-modal id="empModal" maxWidth="2xl" wire:model="editSalary" centered>
        <div class="p-4">
            <div class="mb-4 py-4 dark:text-gray-50 text-slate-900 font-bold text-lg">
                {{ $addSalary ? 'Add' : 'Edit' }} Salary
                <button @click="show = false" class="float-right focus:outline-none" wire:click='resetVariables'>
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form wire:submit.prevent='saveSalary'>
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-full sm:col-span-1">
                        <label for="sg"
                            class="block text-sm font-medium text-gray-700 dark:text-slate-400">Salary Grade</label>
                        <select id="sg" wire:model.live='sg'
                            class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                            <option value="">Select Salary Grade</option>
                            @foreach ($salaryGrades as $sg)
                                <option value="{{ $sg->salary_grade }}">{{ $sg->salary_grade }}</option>
                            @endforeach
                        </select>
                        @error('sg')
                            <span class="text-red-500 text-sm">The Salary Grade is required!</span>
                        @enderror
                    </div>

                    <div class="col-span-full sm:col-span-1">
                        <label for="step"
                            class="block text-sm font-medium text-gray-700 dark:text-slate-400">Step</label>
                        <select id="step" wire:model.live='step'
                            class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                        </select>
                        @error('step')
                            <span class="text-red-500 text-sm">The step is required!</span>
                        @enderror
                    </div>

                    <div class="col-span-full sm:col-span-1">
                        <label for="basicSalary"
                            class="block text-sm font-medium text-gray-700 dark:text-slate-400">Basic Salary</label>
                        <input type="number" step="0.01" id="basicSalary" wire:model='basicSalary'
                            class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700"
                            >
                        @error('basicSalary')
                            <span class="text-red-500 text-sm">The rate per month is required!</span>
                        @enderror
                    </div>

                    <div class="col-span-full sm:col-span-1">
                        <label for="pera"
                            class="block text-sm font-medium text-gray-700 dark:text-slate-400">Personal Economic
                            Relief Allowance</label>
                        <input type="number" step="0.01" id="pera"
                            wire:model.live='pera'
                            class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                    </div>


                    <div class="mt-4 flex justify-end col-span-2">
                        <button class="mr-2 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            <div wire:loading wire:target="saveSalary" class="spinner-border small text-primary" role="status">
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
    <x-modal id="deleteModal" maxWidth="md" wire:model="deleteId" centered>
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
            <form wire:submit.prevent='deleteData'>
                <div class="mt-4 flex justify-end col-span-1 sm:col-span-1">
                    <button class="mr-2 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        <div wire:loading wire:target="deleteData" style="margin-bottom: 5px;">
                            <div class="spinner-border small text-primary" role="status">
                            </div>
                        </div>
                        Delete
                    </button>
                    <p @click="show = false" class="bg-gray-400 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded cursor-pointer">
                        Cancel
                    </p>
                </div>
            </form>

        </div>
    </x-modal>

</div>

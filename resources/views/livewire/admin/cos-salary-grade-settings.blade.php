<div class="col-span-full sm:col-span-12  bg-gray-200 dark:bg-gray-700 rounded-lg shadow block">
    <div class="flex flex-col items-center w-full pb-2 my-4">
        <div class="flex justify-center sm:justify-between items-center mb-4 w-full px-4">
            <h3 class="p-4 text-lg font-semibold text-black dark:text-gray-200 uppercase">Contract of Service</h3>
            <div class="flex items-center">
                <input type="file" id="salaryGrade2" wire:model.live="file" style="display: none;" accept=".xlsx, .xls">
                @if($file)
                    <p class="text-xs text-gray-600 dark:text-gray-100 mr-2">File selected: {{ $file->getClientOriginalName() }}</p>
                @endif
                @error('file') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                <button type="button" class="text-xs bg-green-500 hover:bg-green-700 text-white py-1 px-2 rounded" title="Import Salary Grade"
                    onclick="document.getElementById('salaryGrade2').click()">
                    <i class="bi bi-upload" wire:target='file' wire:loading.remove></i>
                    <div wire:loading wire:target="file">
                        <div class="spinner-border small text-primary" role="status">
                        </div>
                    </div>
                </button>
                <button type="button" class="text-xs bg-green-500 hover:bg-green-700 text-white py-1 px-2 rounded ml-2" title="Export Salary Grade" wire:click='exportSalaryGrade'>
                    <i class="bi bi-download" wire:target='exportSalaryGrade' wire:loading.remove></i>
                    <div wire:loading wire:target="exportSalaryGrade">
                        <div class="spinner-border small text-primary" role="status">
                        </div>
                    </div>
                </button>
            </div>
        </div>
        <div class="w-full overflow-hidden border dark:border-gray-700">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white dark:bg-gray-500">
                    <thead class="bg-gray-100 dark:bg-slate-800 rounded-xl">
                        <tr class="text-gra7-900 dark:text-gray-100 uppercase leading-normal" style="font-size: 11px">
                            <th class="py-2 px-2 text-left">SG</th>
                            <th class="py-2 px-2 text-left">Step 1</th>
                            <th class="py-2 px-2 text-left">Step 2</th>
                            <th class="py-2 px-2 text-left">Step 3</th>
                            <th class="py-2 px-2 text-left">Step 4</th>
                            <th class="py-2 px-2 text-left">Step 5</th>
                            <th class="py-2 px-2 text-left">Step 6</th>
                            <th class="py-2 px-2 text-left">Step 7</th>
                            <th class="py-2 px-2 text-left">Step 8</th>
                            <th class="py-2 px-2 text-center sticky right-0 z-10 bg-gray-100 dark:bg-slate-800">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-gray-400 dark:bg-gray-700" style="font-size: 11px" wire:target='file' wire:loading.remove>
                        @foreach ($salaryGrades as $salaryGrade)
                            <tr class="border-b border-gray-200 hover:bg-gray-100 !hover:text-gray-800">
                                <td class="py-2 px-2 text-left whitespace-nowrap text-gray-800 dark:text-gray-300">
                                    {{ $salaryGrade->salary_grade }}
                                </td>
                                @for ($i = 1; $i <= 8; $i++)
                                    <td class="py-2 px-2 text-left">
                                        {{ number_format($salaryGrade->{"step$i"}, 2) }}
                                    </td>
                                @endfor
                                <td class="py-2 px-2 text-center sticky right-0 z-10 bg-white dark:bg-gray-700">
                                    <div class="relative">
                                        <button wire:click="editSG({{ $salaryGrade->id }})"
                                            class="peer inline-flex items-center justify-center px-4 py-2 -m-5
                                            -mr-2 text-xs font-medium tracking-wide text-blue-500 hover:text-blue-600
                                            focus:outline-none" title="Edit">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>
                                        <button wire:click="toggleDeleteSG({{ $salaryGrade->id }}, 'Salary Grade')"
                                            class="text-xs text-red-600 hover:text-red-900 dark:text-red-600
                                            dark:hover:text-red-900" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="text-xs flex justify-center items-center mt-4" wire:target='file' wire:loading.remove>
                <button wire:click="openSGModal" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded">
                    Add Salary Grade
                </button>
            </div>
        </div>
    </div>


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
                Are you sure you want to delete this {{ $deleteMessage }}?
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

    {{-- Add Salary Grade Modal --}}
    <x-modal id="addSalaryGradeModal" maxWidth="2xl" wire:model="showSGModal">
        <div class="p-4">
            <div class="bg-slate-800 rounded-lg mb-4 dark:bg-gray-200 p-4 text-gray-50 dark:text-slate-900 font-bold uppercase">
                {{ $isEditing ? 'Edit' : 'Add' }} Salary Grade
                <button @click="show = false" class="float-right focus:outline-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            {{-- Form fields --}}
            <form wire:submit.prevent='saveSalaryGrade'>
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label for="salary_grade" class="block text-sm font-medium text-gray-700 dark:text-slate-400 uppercase">Salary Grade</label>
                        <input type="number" id="salary_grade" wire:model='salaryGradeData.salary_grade' {{ $isEditing ? 'readonly' : '' }}
                        class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700" required>
                        @error('salaryGradeData.salary_grade') <span class="text-red-500 text-sm">This field is required!</span> @enderror
                    </div>
                    @for ($i = 1; $i <= 8; $i++)
                        <div class="col-span-full sm:col-span-1">
                            <label for="step{{ $i }}" class="block text-sm font-medium text-gray-700 dark:text-slate-400 uppercase">Step {{ $i }}</label>
                            <input type="number" step="0.01" id="step{{ $i }}" wire:model='salaryGradeData.step{{ $i }}' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                            @error('salaryGradeData.step'.$i) <span class="text-red-500 text-sm">This field is required!</span> @enderror
                        </div>
                    @endfor
                    <div class="mt-4 flex justify-end col-span-2">
                        <button type="submit" class="mr-2 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            <div wire:loading wire:target="saveSalaryGrade" class="spinner-border small text-primary" role="status">
                            </div>
                            Save
                        </button>
                        <p @click="show = false" class="bg-gray-400 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded cursor-pointer">
                            Cancel
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>

</div>
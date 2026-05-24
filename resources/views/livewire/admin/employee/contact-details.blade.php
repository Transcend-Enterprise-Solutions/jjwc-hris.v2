<div class="w-full">

    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3 flex items-center">
        <i class="bi bi-person-rolodex mr-2 text-blue-600"></i>
        Contact Details
    </h3>

    <div class="mt-6 grid grid-cols-2 gap-4">
        <div class="col-span-full sm:col-span-1 flex items-start justify-start   gap-2 text-xs">
            <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Email:</span>
            <span class="text-gray-900 dark:text-gray-100">{{ $selectedUser->userData->email }}</span>
        </div>
        <div class="col-span-full sm:col-span-1 flex items-start justify-start   gap-2 text-xs">
            <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Telephone No.:</span>
            <span class="text-gray-900 dark:text-gray-100">{{ $selectedUser->userData->tel_number }}</span>
        </div>
        <div class="col-span-full sm:col-span-1 flex items-start justify-start   gap-2 text-xs">
            <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Mobile No.:</span>
            <span class="text-gray-900 dark:text-gray-100">{{ $selectedUser->userData->mobile_number }}</span>
        </div>
    </div>

    <p class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-3 flex items-center mt-6">
        <i class="bi bi-geo-alt mr-2"></i>
        Permanent Address
    </p>

    <div class="grid grid-cols-2 gap-4">
        <div class="col-span-full sm:col-span-1 flex items-start justify-start   gap-2 text-xs">
            <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Blk. and Lot:</span>
            <span class="text-gray-900 dark:text-gray-100">
                {{ $selectedUser->userData->p_house_street }}
            </span>
        </div>
        <div class="col-span-full sm:col-span-1 flex items-start justify-start   gap-2 text-xs">
            <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Barangay:</span>
            <span class="text-gray-900 dark:text-gray-100">
                {{ $selectedUser->userData->permanent_selectedBarangay }} 
            </span>
        </div>
        <div class="col-span-full sm:col-span-1 flex items-start justify-start   gap-2 text-xs">
            <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">City/Municipality:</span>
            <span class="text-gray-900 dark:text-gray-100">
                {{ $selectedUser->userData->permanent_selectedCity }} 
            </span>
        </div>
        <div class="col-span-full sm:col-span-1 flex items-start justify-start   gap-2 text-xs">
            <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Province:</span>
            <span class="text-gray-900 dark:text-gray-100">
                {{ $selectedUser->userData->permanent_selectedProvince }}
            </span>
        </div>
        <div class="col-span-full sm:col-span-1 flex items-start justify-start   gap-2 text-xs">
            <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Zipcode:</span>
            <span class="text-gray-900 dark:text-gray-100">
                {{ $selectedUser->userData->permanent_selectedZipcode }}
            </span>
        </div>
    </div>

    <p class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-3 flex items-center mt-6">
        <i class="bi bi-geo-alt mr-2"></i>
        Residential Address
    </p>

    <div class="grid grid-cols-2 gap-4">
        <div class="col-span-full sm:col-span-1 flex items-start justify-start   gap-2 text-xs">
            <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Blk. and Lot:</span>
            <span class="text-gray-900 dark:text-gray-100">
                {{ $selectedUser->userData->r_house_street }}
            </span>
        </div>
        <div class="col-span-full sm:col-span-1 flex items-start justify-start   gap-2 text-xs">
            <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Barangay:</span>
            <span class="text-gray-900 dark:text-gray-100">
                {{ $selectedUser->userData->residential_selectedBarangay }} 
            </span>
        </div>
        <div class="col-span-full sm:col-span-1 flex items-start justify-start   gap-2 text-xs">
            <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">City/Municipality:</span>
            <span class="text-gray-900 dark:text-gray-100">
                {{ $selectedUser->userData->residential_selectedCity }} 
            </span>
        </div>
        <div class="col-span-full sm:col-span-1 flex items-start justify-start   gap-2 text-xs">
            <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Province:</span>
            <span class="text-gray-900 dark:text-gray-100">
                {{ $selectedUser->userData->residential_selectedProvince }}
            </span>
        </div>
        <div class="col-span-full sm:col-span-1 flex items-start justify-start   gap-2 text-xs">
            <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Zipcode:</span>
            <span class="text-gray-900 dark:text-gray-100">
                {{ $selectedUser->userData->residential_selectedZipcode }}
            </span>
        </div>
    </div>

    <div class="w-full mt-8 flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3 flex items-center mt-8">
            <i class="bi bi-person-rolodex mr-2 text-blue-600"></i>
            Emergency Contacts
        </h3>
        <button wire:click="addNewContact({{ $selectedUser->id }})"
                class="px-2 py-1.5 bg-green-500 text-white rounded-md text-xs
                hover:bg-green-600 focus:outline-none dark:bg-gray-700
                dark:hover:bg-green-600 dark:text-gray-300 dark:hover:text-white">
            <i class="bi bi-plus-lg mr-2"></i> Add Contact
        </button>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-full">
                <thead class="bg-gray-200 dark:bg-gray-700 rounded-xl">
                    <tr class="whitespace-nowrap">
                        <th scope="col" class="px-5 py-3 text-xs font-medium text-left uppercase">
                            Name
                        </th>
                        <th scope="col" class="px-5 py-3 text-xs font-medium text-center uppercase">
                            Relationship
                        </th>
                        <th scope="col" class="px-5 py-3 text-xs font-medium text-center uppercase">
                            Telephone Number
                        </th>
                        <th scope="col" class="px-5 py-3 text-xs font-medium text-center uppercase">
                            Mobile Number
                        </th>
                        <th class="px-5 py-3 text-xs font-medium text-center uppercase sticky right-0 z-10">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @foreach($emergencyContacts as $contact)
                        <tr class="text-neutral-800 dark:text-neutral-200">
                            <td class="px-5 py-4 text-left text-xs font-medium whitespace-nowrap">
                                {{ $contact->name }}
                            </td>
                            <td class="px-5 py-4 text-center text-xs font-medium whitespace-nowrap">
                                {{ $contact->relationship }}
                            </td>
                            <td class="px-5 py-4 text-center text-xs font-medium whitespace-nowrap">
                                {{ $contact->tel_number }}
                            </td>
                            <td class="px-5 py-4 text-center text-xs font-medium whitespace-nowrap">
                                {{ $contact->mobile_number }}
                            </td>
                            <td class="px-5 py-4 text-sm font-medium text-center whitespace-nowrap sticky right-0 z-10 bg-white dark:bg-gray-800">
                                <div class="flex items-center justify-center space-x-2">
                                    <button wire:click="editNewContact({{ $contact->id }})"
                                            class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200" 
                                            title="Edit Contract">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                    <button wire:click="toggleDelete({{ $contact->id }})"
                                            class="text-sm text-red-600 hover:text-red-900 dark:text-red-600 dark:hover:text-red-900" 
                                            title="Delete Contract">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if ($emergencyContacts->isEmpty())
            <div class="p-4 text-center text-gray-500 dark:text-gray-300">
                No records!
            </div>
        @endif
    </div>

    {{-- Add and Edit Employee Pos Modal --}}
    <x-modal id="empModal" maxWidth="2xl" wire:model="editContact" centered>
        <div class="p-4">
            <div class="mb-4 py-4 dark:text-gray-50 text-slate-900 font-bold text-lg">
                {{ $addContact ? 'Add' : 'Edit' }} Emergency Contact
                <button @click="show = false" class="float-right focus:outline-none" wire:click='resetVariables'>
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form wire:submit.prevent='saveContact'>
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-full sm:col-span-1">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Name <span class="text-red-500">*</span></label>
                        <input type="text" id="name" wire:model='name' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                        @error('name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-span-full sm:col-span-1">
                        <label for="relationship" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Relationship <span class="text-red-500">*</span></label>
                        <input type="text" id="relationship" wire:model='relationship' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                        @error('relationship')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-span-full sm:col-span-1">
                        <label for="telNumber" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Telephone Number (Optional)</label>
                        <input type="text" id="telNumber" wire:model='telNumber' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                        @error('telNumber')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-span-full sm:col-span-1">
                        <label for="mobileNumber" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Mobile Number <span class="text-red-500">*</span></label>
                        <input type="text" id="mobileNumber" wire:model='mobileNumber' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                        @error('mobileNumber')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>


                    <div class="mt-4 flex justify-end col-span-2">
                        <button class="mr-2 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            <div wire:loading wire:target="saveContact" class="spinner-border small text-primary" role="status">
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

<div class="w-full flex flex-col justify-center">

    <style>
        html {
            scroll-behavior: smooth;
        }

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
            color: rgb(255, 255, 255);
        }
    </style>

    <div class="flex justify-center w-full">
        <div class="w-full bg-white rounded-2xl p-3 sm:p-6 shadow dark:bg-gray-800 overflow-x-visible">

            @if(!$registerEmployee)
            
            <div class="pb-4 mb-3 pt-4 sm:pt-0">
                <h1 class="text-lg font-bold text-center text-slate-800 dark:text-white">Employee Registrations</h1>
            </div>

            <div class="mb-6 flex flex-col sm:flex-row items-end justify-between">
                <div class="w-full sm:w-auto">
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Search</label>
                    <input type="text" id="search" wire:model.live="search"
                        class="px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-400 hover:bg-gray-300 rounded-md
                            dark:hover:bg-slate-600 dark:border-slate-600
                            dark:text-gray-300 dark:bg-gray-800"
                        placeholder="Enter email ...">
                </div>

                <div class="w-full sm:w-auto">
                    <button wire:click="toggleAddRegOtp" 
                        class="text-sm mt-4 sm:mt-1 px-2 py-1.5 bg-green-500 text-white rounded-md 
                        hover:bg-green-600 focus:outline-none dark:bg-gray-700 w-full
                        dark:hover:bg-green-600 dark:text-gray-300 dark:hover:text-white">
                        Send Registration OTP
                    </button>
                    <button wire:click="toggleRegEmployee" 
                        class="text-sm mt-4 sm:mt-1 px-2 py-1.5 bg-green-500 text-white rounded-md 
                        hover:bg-green-600 focus:outline-none dark:bg-gray-700 w-full
                        dark:hover:bg-green-600 dark:text-gray-300 dark:hover:text-white">
                        <i class="bi bi-person-add mr-1 text-md"></i> Register Employee
                    </button>
                </div>
            </div>

             <!-- Table -->
             <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto">
                    <div class="inline-block w-full py-2 align-middle">
                        <div class="overflow-hidden border dark:border-gray-700 rounded-lg">
                            <div>
                                <div class="overflow-x-auto">
                                    <table class="w-full min-w-full">
                                        <thead class="bg-gray-200 dark:bg-gray-700 rounded-xl">
                                            <tr class="whitespace-nowrap">
                                                <th scope="col" class="px-5 py-3 text-left text-sm font-medium uppercase">
                                                    Email
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    OTP
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Provided By
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Date Provided
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Status
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Employee Registered
                                                </th>
                                                  <th
                                                    class="px-5 py-3 text-gray-100 text-sm font-medium text-right sticky right-0 z-10 bg-gray-600 dark:bg-gray-600">
                                                    Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-neutral-200 dark:divide-gray-400">
                                                @foreach($registrations as $regs)
                                                    <tr class="text-neutral-800 dark:text-neutral-200">
                                                        <td class="px-5 py-4 text-left text-sm font-medium whitespace-nowrap">
                                                            {{ $regs->email }}
                                                        </td>
                                                        <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap">
                                                            {{ substr($regs->otp, 0, 1) . str_repeat('*', 4) . substr($regs->otp, -1) }}
                                                        </td>
                                                        <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap">
                                                            {{ $regs->admin }}
                                                        </td>
                                                        <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap">
                                                            {{ $regs->date_provided }}
                                                        </td>
                                                        <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap">
                                                            @if($regs->status == 0)
                                                                <span class="bg-orange-200 dark:bg-orange-500 pt-1 pb-2 px-2 rounded-md">Pending</span>
                                                            @elseif($regs->status == 1)
                                                                <span class="bg-green-200 dark:bg-green-500 pt-1 pb-2 px-2 rounded-md">Registered</span>
                                                            @elseif($regs->status == 2)
                                                                <span class="bg-red-200 dark:bg-red-500 pt-1 pb-2 px-2 rounded-md">Expired</span>
                                                            @endif
                                                        </td>
                                                        <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap">
                                                            @if($regs->status == 1)
                                                                <a href="{{ route('/employee-management/employees', ['search' => $regs->user]) }}" target="_blank">
                                                                    <span class="text-blue-500 hover:underline">{{ $regs->user }}</span>
                                                                </a>
                                                            @else
                                                                --
                                                            @endif
                                                        </td>
                                                        <td
                                                            class="px-5 py-4 text-sm font-medium text-center whitespace-nowrap sticky right-0 z-10 bg-white dark:bg-gray-800">
                                                            <div class="relative">
                                                                <button wire:click="toggleDelete({{ $regs->id }})"
                                                                    class=" text-red-600 hover:text-red-900 dark:text-red-600 
                                                                        dark:hover:text-red-900"
                                                                    title="Delete">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                        </tbody>
                                    </table>
                                    @if ($registrations->isEmpty())
                                        <div class="p-4 text-center text-gray-500 dark:text-gray-300">
                                            No records!
                                        </div> 
                                    @endif
                                </div>
                                <div class="p-5 text-neutral-500 dark:text-neutral-200 bg-gray-200 dark:bg-gray-700">
                                    {{ $registrations->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @else
                <div class="w-full flex justify-end relative">
                    <button
                        class="text-xs inline-flex items-center justify-center h-10 gap-3 px-5 font-medium text-gray-800 dark:text-white bg-gray-200 dark:bg-gray-700 rounded-xl hover:bg-gray-500 focus:ring-2 focus:ring-offset-2 focus:ring-black"
                        wire:click="set('registerEmployee', false)">
                        <span>Cancel</span>
                    </button>
                </div>
                @livewire('admin.employee.registration')

            @endif
        </div>
    </div>



    {{-- Register OTP Modal --}}
    <x-modal id="regOtp" maxWidth="2xl" wire:model="genOtp" centered>
        <div class="p-4">
            <div class="flex items-center justify-between pb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">
                    Send Registration OTP
                </h3>
                <button @click="show = false" class="float-right focus:outline-none" wire:click='resetVariables'>
                    <i class="fas fa-times"></i>
                </button>
            </div>
            {{-- Form fields --}}
            <form wire:submit.prevent='submitRegOtp'>
                <div class="grid grid-cols-2 gap-4">
                    
                    <div class="col-span-2">
                        <label for="userId" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Email</label>
                        <input type="email" id="userId" wire:model.live='email' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                        @error('email') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>
                 
                    {{-- Save and Cancel buttons --}}
                    <div class="mt-4 flex justify-end col-span-2">
                        <button class="mr-2 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            <div wire:loading wire:target="submitRegOtp" style="margin-right: 5px">
                                <div class="spinner-border small text-primary" role="status">
                                </div>
                            </div>
                            Send
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
                Are you sure you want to delete this registration OTP?
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
                    <p @click="show = false"
                        class="bg-gray-400 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded cursor-pointer">
                        Cancel
                    </p>
                </div>
            </form>

        </div>
    </x-modal>


</div>
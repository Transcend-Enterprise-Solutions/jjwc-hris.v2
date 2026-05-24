<div class="w-full">
    <!-- Table -->
    <div class="w-full flex justify-center">
        <div class="flex justify-center w-full">
            <div class="w-full bg-white rounded-2xl p-3 sm:p-8 shadow dark:bg-gray-800">
                <div class="pb-4 pt-4 sm:pt-1">
                    <h1 class="text-lg font-bold text-center text-slate-800 dark:text-white">Leave Credits</h1>
                </div>

                <div class="flex flex-col mb-4">
                    <div>
                        <label for="search"
                            class="block text-sm font-medium text-gray-700 dark:text-slate-400">Search</label>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4">
                        <!-- Search input -->
                        <div class="w-full sm:w-auto">
                            <input type="search" id="search" wire:model.live="search"
                                placeholder="Enter employee name"
                                class="py-2 px-3 mt-1 block w-full sm:w-80 shadow-sm text-sm font-medium border-gray-400 dark:border-gray-600 rounded-md dark:text-gray-300 dark:bg-gray-800 outline-none focus:outline-none">
                        </div>
                        <!-- Button aligned with input field -->
                        <div class="flex w-full sm:w-32 justify-between mt-2 sm:mt-0 sm:justify-start sm:space-x-4">
                            <button wire:click="openInputCredits"
                                class="text-sm mt-4 sm:mt-1 px-2 py-1.5 bg-green-500 text-white rounded-md hover:bg-green-600 focus:outline-none dark:bg-gray-700 w-full dark:hover:bg-green-600 dark:text-gray-300 dark:hover:text-white">
                                Input Credits
                            </button>
                        </div>
                    </div>

                </div>

                {{-- Table --}}
                <div class="overflow-x-auto text-xs">
                    <div class="overflow-hidden border dark:border-gray-700 rounded-t-lg">
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white dark:bg-gray-800 overflow-hidden">
                                <thead class="bg-gray-200 dark:bg-gray-700 rounded-xl">
                                    <tr class="whitespace-nowrap">
                                        <th scope="col" class="px-4 uppercase font-medium py-3 text-left">Employee</th>
                                        <th scope="col" class="px-4 uppercase font-medium py-3 text-center">VL Credits</th>
                                        <th scope="col" class="px-4 uppercase font-medium py-3 text-center">SL Credits</th>
                                        {{-- <th scope="col" class="px-4 uppercase font-medium py-3 text-center">SPL Credits</th>
                                        <th scope="col" class="px-4 uppercase font-medium py-3 text-center">CTO Credits</th> --}}
                                        <th scope="col" class="px-4 uppercase font-medium py-3 text-center">Updated as of</th>
                                        <th scope="col" class="px-4 uppercase font-medium py-3 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($leaveCredits->count() > 0)
                                        @foreach ($leaveCredits as $leaveCredit)
                                            <tr class="whitespace-nowrap border-b dark:border-gray-700 dark:text-neutral-200">
                                                <td class="px-4 py-2 text-left">
                                                    <div class="flex gap-3 items-center">
                                                        @if ($leaveCredit->user->profile_photo_path)
                                                            <img src="{{ route('profile-photo.file', ['filename' => basename($leaveCredit->user->profile_photo_path)]) }}"
                                                                alt="{{ $leaveCredit->user?->name ?? 'No User Assigned' }}"
                                                                width="32" height="32"
                                                                class="w-10 h-10 rounded-full object-cover border border-gray-500">
                                                        @else
                                                            <div class="w-10 h-10 rounded-full bg-gray-500 border border-gray-500 dark:bg-gray-600 flex items-center justify-center text-white text-xs font-medium">
                                                                {{ strtoupper(substr(($leaveCredit->user?->name ?? 'No User Assigned'), 0, 1)) }}{{ strtoupper(substr(explode(' ', ($leaveCredit->user?->name ?? 'No User Assigned'))[1] ?? '', 0, 1)) }}
                                                            </div>
                                                        @endif
                                                        <span>{{ $leaveCredit->user->name }}</span>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-2 text-center">
                                                    {{ number_format($leaveCredit->vl_claimable_credits ?? 0, 3) }}</td>
                                                <td class="px-4 py-2 text-center">
                                                    {{ number_format($leaveCredit->sl_claimable_credits ?? 0, 3) }}</td>
                                                {{-- <td class="px-4 py-2 text-center">
                                                    {{ number_format($leaveCredit->spl_claimable_credits ?? 0, 3) }}</td>
                                                <td class="px-4 py-2 text-center">
                                                    {{ number_format($leaveCredit->cto_claimable_credits ?? 0, 3) }}</td> --}}
                                                <td class="px-4 py-2 text-center">
                                                    {{ \Carbon\Carbon::today()->format('M d, Y') }}
                                                </td>
                                                <td class="px-4 py-2 text-center">
                                                    <button wire:click="openEditCredits({{ $leaveCredit->user_id }})"
                                                        class="peer inline-flex items-center justify-center px-4 py-2 -m-5 
                                                                            -mr-2 text-sm font-medium tracking-wide text-blue-500 hover:text-blue-600 
                                                                            focus:outline-none">
                                                        <i class="fas fa-pencil-alt ml-3" title="Edit"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                     @else
                                        <tr>
                                            <td colspan="5" class="p-4 text-center text-sm text-gray-500 dark:text-gray-300">
                                                No records!
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="p-5 text-neutral-500 dark:text-neutral-200 bg-gray-200 dark:bg-gray-700 rounded-b-lg">
                    {{ $leaveCredits->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Input Credits --}}
    <x-modal id="inputCredits" maxWidth="lg" wire:model="inputCredits">
        <div class="p-6">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">
                    Input Credits
                </h3>
                <button wire:click="closeInputCredits"
                    class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 focus:outline-none">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <form class="space-y-6" wire:submit.prevent="saveCredits">
                <!-- Employee Selection -->
                <div>
                    <label for="employee" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Employee
                    </label>
                    <select id="employee" wire:model="employee"
                        class="w-full p-2 mt-1 border rounded-md text-gray-700 dark:text-gray-300 dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select an employee</option>
                        @foreach ($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                        @endforeach
                    </select>
                    @error('employee')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex flex-col md:flex-row md:space-x-4">
                    <!-- Claimable Credits -->
                    <div class="w-full">
                        <label for="vlClaimableCredits"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">VL Credits</label>
                        <input id="vlClaimableCredits" type="text" wire:model="vlClaimableCredits"
                            class="w-full p-2 mt-1 border rounded-md text-gray-700 dark:text-gray-300 dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter claimable credits">
                        @error('vlClaimableCredits')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-col md:flex-row md:space-x-4">
                    <!-- Claimable Credits -->
                    <div class="w-full">
                        <label for="slClaimableCredits"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">SL Credits</label>
                        <input id="slClaimableCredits" type="text" wire:model="slClaimableCredits"
                            class="w-full p-2 mt-1 border rounded-md text-gray-700 dark:text-gray-300 dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter claimable credits">
                        @error('slClaimableCredits')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                {{-- <div class="flex flex-col md:flex-row md:space-x-4">
                    <!-- Claimable Credits -->
                    <div class="w-full">
                        <label for="splClaimableCredits"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">SPL Credits</label>
                        <input id="splClaimableCredits" type="text" wire:model="splClaimableCredits"
                            class="w-full p-2 mt-1 border rounded-md text-gray-700 dark:text-gray-300 dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter claimable credits">
                        @error('splClaimableCredits')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                </div> --}}

                {{-- <div class="flex flex-col md:flex-row md:space-x-4">
                    <!-- Claimable Credits -->
                    <div class="w-full">
                        <label for="ctoClaimableCredits"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">CTO Credits</label>
                        <input id="ctoClaimableCredits" type="text" wire:model="ctoClaimableCredits"
                            class="w-full p-2 mt-1 border rounded-md text-gray-700 dark:text-gray-300 dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter claimable credits">
                        @error('ctoClaimableCredits')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div> --}}

                <!-- Action Buttons -->
                <div class="mt-6 flex justify-end space-x-4">
                    <!-- Save Button -->
                    <button type="submit"
                        class="px-4 py-2 rounded-md bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800">
                        Save
                    </button>

                    <!-- Close Button -->
                    <button type="button" wire:click="closeInputCredits"
                        class="px-4 py-2 rounded-md bg-gray-700 hover:bg-gray-800 text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:focus:ring-offset-gray-800">
                        Close
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    {{-- Edit Credits --}}
    <x-modal id="editCredits" maxWidth="lg" wire:model="editCredits">
        <div class="p-6">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">
                    Edit Credits
                </h3>
                <button wire:click="closeEditCredits"
                    class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 focus:outline-none">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <form class="space-y-6" wire:submit.prevent="updateCredits">

                {{-- @if ($credits_inputted != 1) --}}
                <div class="flex flex-col md:flex-row md:space-x-4">
                    <!-- Claimable Credits -->
                    <div class="w-full">
                        <label for="vlClaimableCredits"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">VL Credits</label>
                        <input id="vlClaimableCredits" type="text" wire:model="vlClaimableCredits"
                            class="w-full p-2 mt-1 border rounded-md text-gray-700 dark:text-gray-300 dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter credits">
                        @error('vlClaimableCredits')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                {{-- @endif --}}

                {{-- @if ($credits_inputted != 1) --}}
                <div class="flex flex-col md:flex-row md:space-x-4">
                    <!-- Claimable Credits -->
                    <div class="w-full">
                        <label for="slClaimableCredits"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">SL Credits</label>
                        <input id="slClaimableCredits" type="text" wire:model="slClaimableCredits"
                            class="w-full p-2 mt-1 border rounded-md text-gray-700 dark:text-gray-300 dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter credits">
                        @error('slClaimableCredits')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                {{-- @endif --}}

                {{-- @if ($credits_inputted != 1) --}}
                {{-- <div class="flex flex-col md:flex-row md:space-x-4">
                    <!-- Claimable Credits -->
                    <div class="w-full">
                        <label for="splClaimableCredits"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">SPL Credits</label>
                        <input id="splClaimableCredits" type="text" wire:model="splClaimableCredits"
                            class="w-full p-2 mt-1 border rounded-md text-gray-700 dark:text-gray-300 dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter credits">
                        @error('splClaimableCredits')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div> --}}
                {{-- @endif --}}

                <!-- CTO Leave Part -->
                {{-- <fieldset class="border border-gray-300 p-4 rounded-lg overflow-hidden w-full h-full mb-4 md:mb-0"> --}}
                {{-- <legend class="text-gray-700 dark:text-slate-100">CTO Leave</legend> --}}
                {{-- <div class="flex flex-col md:flex-row md:space-x-4">
                    <!-- Claimable Credits -->
                    <div class="w-full">
                        <label for="ctoClaimableCredits"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">CTO Credits</label>
                        <input id="ctoClaimableCredits" type="text" wire:model="ctoClaimableCredits"
                            class="w-full p-2 mt-1 border rounded-md text-gray-700 dark:text-gray-300 dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter credits">
                        @error('ctoClaimableCredits')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div> --}}

                <!-- Claimed Credits -->
                {{-- <div class="w-full md:w-1/2 mt-4 md:mt-0">
                            <label for="splClaimedCredits"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Claimed
                                Credits</label>
                            <input id="splClaimedCredits" type="number" wire:model="splClaimedCredits"
                                class="w-full p-2 mt-1 border rounded-md text-gray-700 dark:text-gray-300 dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Enter claimed credits">
                            @error('splClaimedCredits')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div> --}}
                {{-- </div> --}}
                {{-- </fieldset> --}}

                <!-- Action Buttons -->
                <div class="mt-6 flex justify-end space-x-4">
                    <!-- Save Button -->
                    <button type="submit"
                        class="px-4 py-2 rounded-md bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800">
                        Update
                    </button>

                    <!-- Close Button -->
                    <button type="button" wire:click="closeEditCredits"
                        class="px-4 py-2 rounded-md bg-gray-700 hover:bg-gray-800 text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:focus:ring-offset-gray-800">
                        Close
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

</div>

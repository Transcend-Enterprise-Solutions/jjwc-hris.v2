<div class="w-full" x-data="{ activeTab: @entangle('activeTab') }">
    <div class="flex justify-center w-full">
        <div class="w-full bg-white rounded-2xl p-3 sm:p-6 shadow dark:bg-gray-800 overflow-x-visible">
            <div class="pb-4 pt-4 sm:pt-1">
                <h1 class="text-lg font-bold text-center text-slate-800 dark:text-white">Mandatory Leave Schedule</h1>
            </div>

            <div class="flex flex-col mb-4">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Search</label>
                <input type="search" id="search" wire:model.debounce.500ms="search" placeholder="Enter employee name"
                    class="py-2 px-3 mt-1 block w-full sm:w-80 shadow-sm text-sm font-medium border-gray-400 dark:border-gray-600 rounded-md dark:text-gray-300 dark:bg-gray-800 outline-none focus:outline-none">
            </div>

            <!-- Tabs -->
            <div class="flex flex-col">
                <div class="flex gap-2 overflow-x-auto -mb-2" role="tablist">
                    <button @click="$wire.setActiveTab('pending')"
                        :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-t-lg': activeTab === 'pending', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': activeTab !== 'pending' }"
                        class="h-min px-4 pt-2 pb-4 text-sm" role="tab">Pending</button>
                    <button @click="$wire.setActiveTab('approved')"
                        :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-t-lg': activeTab === 'approved', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': activeTab !== 'approved' }"
                        class="h-min px-4 pt-2 pb-4 text-sm" role="tab">Approved</button>
                    <button @click="$wire.setActiveTab('disapproved')"
                        :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-t-lg': activeTab === 'disapproved', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': activeTab !== 'disapproved' }"
                        class="h-min px-4 pt-2 pb-4 text-sm" role="tab">Disapproved</button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <div class="overflow-hidden border dark:border-gray-700 rounded-lg">
                    <table class="w-full min-w-full">
                        <thead class="bg-gray-200 dark:bg-gray-700 rounded-xl">
                            <tr class="whitespace-nowrap">
                                <th class="px-5 py-3 text-sm font-medium uppercase text-left"
                                    @if (in_array($activeTab, ['approved', 'disapproved'])) style="width: 25%;" @endif>
                                    Name
                                </th>
                                <th class="px-5 py-3 text-sm font-medium uppercase text-center"
                                    @if (in_array($activeTab, ['approved', 'disapproved'])) style="width: 25%;" @endif>
                                    Date Requested
                                </th>
                                <th class="px-5 py-3 text-sm font-medium uppercase text-center"
                                    @if (in_array($activeTab, ['approved', 'disapproved'])) style="width: 25%;" @endif>
                                    Date Approved
                                </th>
                                <th class="px-5 py-3 text-sm font-medium uppercase text-center"
                                    @if (in_array($activeTab, ['approved', 'disapproved'])) style="width: 25%;" @endif>
                                    Status
                                </th>
                                @if ($activeTab === 'pending')
                                    <th
                                        class="px-5 py-3 text-sm font-medium text-center uppercase sticky right-0 z-10 bg-gray-600 dark:bg-gray-600">
                                        Action
                                    </th>
                                @endif
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-neutral-200 dark:divide-gray-400">
                            @forelse($mandatoryLeaves as $leave)
                                <tr class="whitespace-nowrap">
                                    <td class="px-4 py-4 text-center">{{ $leave->user->name }}</td>
                                    <td class="px-4 py-4 text-center">{{ $leave->date_requested->format('F j, Y') }}
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        {{ $leave->status === 'approved' ? $leave->updated_at->format('F j, Y') : 'N/A' }}
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span
                                            class="px-3 py-1 text-sm font-semibold rounded-full
                                            {{ $leave->status == 'approved' ? 'bg-green-500 text-white' : ($leave->status == 'pending' ? 'bg-yellow-500 text-white' : 'bg-red-500 text-white') }}">
                                            {{ ucfirst($leave->status) }}
                                        </span>
                                    </td>
                                    @if ($activeTab === 'pending')
                                        <td
                                            class="px-5 py-4 text-sm font-medium text-center sticky right-0 z-10 bg-white dark:bg-gray-800">
                                            <div class="flex justify-center items-center">
                                                <button wire:click="confirmApproval({{ $leave->id }})"
                                                    class="text-blue-500 mx-2">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                                <button wire:click="confirmDisapproval({{ $leave->id }})"
                                                    class="text-red-500">
                                                    <i class="bi bi-x"></i>
                                                </button>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $activeTab === 'pending' ? 5 : 4 }}"
                                        class="p-4 text-center text-gray-500 dark:text-gray-300">
                                        No records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="p-5 text-neutral-500 dark:text-neutral-200 bg-gray-200 dark:bg-gray-700">
                        {{ $mandatoryLeaves->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-modal maxWidth="lg" wire:model="showApproveModal" centered>
        <div class="p-6">
            <div class="flex items-center justify-between pb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">
                    Are you sure you want to approve this request?
                </h3>
                <button wire:click="$set('showApproveModal', false)"
                    class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 focus:outline-none">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <form class="space-y-6" wire:submit.prevent="approveLeave">
                <div class="mt-6 flex justify-end space-x-4">
                    <button type="submit"
                        class="px-4 py-2 rounded-md bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium focus:outline-none">
                        Yes
                    </button>
                    <button type="button" wire:click="$set('showApproveModal', false)"
                        class="px-4 py-2 rounded-md bg-gray-700 hover:bg-gray-800 text-white text-sm font-medium focus:outline-none">
                        No
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal maxWidth="lg" wire:model="showDisapproveModal" centered>
        <div class="p-6">
            <div class="flex items-center justify-between pb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">
                    Are you sure you want to disapprove this request?
                </h3>
                <button wire:click="$set('showDisapproveModal', false)"
                    class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 focus:outline-none">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <form class="space-y-6" wire:submit.prevent="disapproveLeave">
                <div class="mt-6 flex justify-end space-x-4">
                    <button type="submit"
                        class="px-4 py-2 rounded-md bg-red-500 hover:bg-red-600 text-white text-sm font-medium focus:outline-none">
                        Yes
                    </button>
                    <button type="button" wire:click="$set('showDisapproveModal', false)"
                        class="px-4 py-2 rounded-md bg-gray-700 hover:bg-gray-800 text-white text-sm font-medium focus:outline-none">
                        No
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

</div>

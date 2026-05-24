<div x-data="{ activeTab: @entangle('activeTab') }" class="w-full">
    <div class="w-full flex justify-center">
        <div class="w-full bg-white rounded-2xl p-3 sm:p-6 shadow dark:bg-gray-800 overflow-x-visible">
            <div class="pb-4 pt-4 sm:pt-1">
                <h1 class="text-lg font-bold text-center text-slate-800 dark:text-white">Leave Records</h1>
            </div>

            <!-- Search input -->
            <div class="relative inline-block text-left mb-4 w-full">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Search</label>
                <input type="search" id="search" wire:model.live="search" placeholder="Enter employee name"
                    class="py-2 px-3 block w-full sm:w-80 shadow-sm text-sm font-medium border-gray-400
                                   rounded-md dark:text-gray-300 dark:bg-gray-800 outline-none focus:outline-none">
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

            <div class="overflow-x-auto text-xs">
                <div class="overflow-hidden border dark:border-gray-700 rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-full">
                            <!-- Table head -->
                            <thead class="bg-gray-200 dark:bg-gray-700 rounded-xl">
                                <tr class="whitespace-nowrap">
                                    <th class="px-5 py-3 text-xs font-medium text-left uppercase">
                                        Employee
                                    </th>
                                    <th class="px-5 py-3 text-xs font-medium uppercase text-center">
                                        Date
                                        of Filing</th>
                                    <th class="px-5 py-3 text-xs font-medium uppercase text-center">
                                        Type
                                        of Leave</th>
                                    <th class="px-5 py-3 text-xs font-medium uppercase text-center">
                                        Details of Leave</th>
                                    @if ($activeTab === 'pending')
                                        <th scope="col"
                                            class="px-5 py-3 text-xs font-medium uppercase text-center">
                                            Requested Day/s
                                        </th>
                                        <th scope="col"
                                            class="px-5 py-3 text-xs font-medium uppercase text-center">
                                            Requested Date/s
                                        </th>
                                    @elseif ($activeTab === 'disapproved')
                                        <th scope="col"
                                            class="px-5 py-3 text-xs font-medium uppercase text-center">
                                            Disapproved Day/s
                                        </th>
                                        <th scope="col"
                                            class="px-5 py-3 text-xs font-medium uppercase text-center">
                                            Disapproved Date/s
                                        </th>
                                    @else
                                        <th scope="col"
                                            class="px-5 py-3 text-xs font-medium uppercase text-center">
                                            Approved Day/s
                                        </th>
                                        <th scope="col"
                                            class="px-5 py-3 text-xs font-medium uppercase text-center">
                                            Approved Date/s
                                        </th>
                                    @endif
                                </tr>
                            </thead>
                            <!-- Table body -->
                            <tbody>
                                @if ($leaveApplications->count() > 0)
                                    @foreach ($leaveApplications as $leaveApplication)
                                        <tr class="whitespace-nowrap border-b dark:border-gray-700 dark:text-neutral-200">
                                            <td class="px-4 py-3 text-left">
                                                <div class="flex gap-3 items-center">
                                                    @if ($leaveApplication->user->profile_photo_path)
                                                        <img src="{{ route('profile-photo.file', ['filename' => basename($leaveApplication->user->profile_photo_path)]) }}"
                                                            alt="{{ $leaveApplication->user?->name ?? 'No User Assigned' }}"
                                                            width="32" height="32"
                                                            class="w-10 h-10 rounded-full object-cover border border-gray-500">
                                                    @else
                                                        <div class="w-10 h-10 rounded-full bg-gray-500 border border-gray-500 dark:bg-gray-600 flex items-center justify-center text-white text-xs font-medium">
                                                            {{ strtoupper(substr(($leaveApplication->user?->name ?? 'No User Assigned'), 0, 1)) }}{{ strtoupper(substr(explode(' ', ($leaveApplication->user?->name ?? 'No User Assigned'))[1] ?? '', 0, 1)) }}
                                                        </div>
                                                    @endif
                                                    <span>{{ $leaveApplication->user->name }}</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                {{ $leaveApplication->date_of_filing }}
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                {{ $leaveApplication->type_of_leave }}
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                {{ $leaveApplication->details_of_leave ?? 'N/A' }}
                                            </td>
                                            @if ($activeTab === 'pending' || $activeTab === 'disapproved')
                                                <td class="px-4 py-3 text-center">
                                                    {{ $leaveApplication->number_of_days }}
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    @if (Str::contains($leaveApplication->list_of_dates, ' - '))
                                                        {{ $leaveApplication->list_of_dates }}
                                                    @else
                                                        <div class="flex flex-col">
                                                            @foreach (explode(',', $leaveApplication->list_of_dates) as $date)
                                                                <span>{{ trim($date) }}</span>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </td>
                                            @else
                                                <td class="px-4 py-3 text-center">
                                                    {{ $leaveApplication->approved_days ?? 'N/A' }}
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    @if (Str::contains($leaveApplication->approved_dates, ' - '))
                                                        {{ $leaveApplication->approved_dates }}
                                                    @else
                                                        <div class="flex flex-col">
                                                            @foreach (explode(',', $leaveApplication->approved_dates) as $date)
                                                                <span>{{ trim($date) }}</span>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="p-4 text-center text-gray-500 dark:text-gray-300">
                                            No {{ $activeTab }} leave request!
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div
                        class="p-5 border-t rounded-b-lg border-gray-200 dark:border-slate-600 text-neutral-500 dark:text-neutral-200 bg-gray-200 dark:bg-gray-700">
                        {{ $leaveApplications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

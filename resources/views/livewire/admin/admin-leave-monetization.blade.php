<div class="w-full">
    {{-- Table --}}
    <div class="w-full flex justify-center">
        <div class="flex justify-center w-full">
            <div class="w-full bg-white rounded-2xl p3 sm:p-8 shadow dark:bg-gray-800 overflow-x-visible">
                <div class="pb-4 pt-4 sm:pt-1">
                    <h1 class="text-lg font-bold text-center text-slate-800 dark:text-white">Monetization Requests</h1>
                </div>

                <!-- Search input -->
                <div class="relative inline-block text-left mb-4 w-full">
                    <label for="search" class="block text-xs font-medium text-gray-700 dark:text-slate-400">Search</label>
                    <input type="search" id="search" wire:model.live="search" placeholder="Enter employee name"
                        class="py-2 px-3 block w-full sm:w-80 shadow-sm text-xs font-medium border-gray-400
                                    rounded-md dark:text-gray-300 dark:bg-gray-800 outline-none focus:outline-none">
                </div>


                <div class="flex flex-col">
                    <div class="overflow-x-auto">
                        <div class="inline-block w-full py-2 align-middle">
                            <div class="overflow-hidden border dark:border-gray-700 rounded-lg">
                                <div class="overflow-x-auto">
                                    <table class="w-full min-w-full">
                                        <thead class="bg-gray-200 dark:bg-gray-700 rounded-xl">
                                            <tr class="whitespace-nowrap">
                                                <th scope="col"
                                                    class="px-5 py-3 text-xs font-medium text-left uppercase">
                                                    Employee</th>
                                                <th scope="col"
                                                    class="px-5 py-3 text-xs font-medium uppercase text-center">
                                                    Requested Credits (VL)</th>
                                                <th scope="col"
                                                    class="px-5 py-3 text-xs font-medium uppercase text-center">
                                                    Requested Credits (SL)</th>
                                                <th scope="col"
                                                    class="px-5 py-3 text-xs font-medium uppercase text-center">
                                                    Monetize Credits (VL)</th>
                                                <th scope="col"
                                                    class="px-5 py-3 text-xs font-medium uppercase text-center">
                                                    Monetize Credits (SL)</th>
                                                <th scope="col"
                                                    class="px-5 py-3 text-xs font-medium uppercase text-center">
                                                    Status</th>
                                                <th scope="col"
                                                    class="px-5 py-3 text-gray-100 text-xs font-medium text-right sticky right-0 z-10 bg-gray-600 dark:bg-gray-600">
                                                    Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($monetizationRequests->count() > 0)
                                                @foreach ($monetizationRequests as $request)
                                                    <tr class="whitespace-nowrap">
                                                        <td class="px-4 py-2 text-left">
                                                            <div class="flex gap-3 items-center">
                                                                @if ($request->user->profile_photo_path)
                                                                    <img src="{{ route('profile-photo.file', ['filename' => basename($request->user->profile_photo_path)]) }}"
                                                                        alt="{{ $request->user?->name ?? 'No User Assigned' }}"
                                                                        width="32" height="32"
                                                                        class="w-10 h-10 rounded-full object-cover border border-gray-500">
                                                                @else
                                                                    <div class="w-10 h-10 rounded-full bg-gray-500 border border-gray-500 dark:bg-gray-600 flex items-center justify-center text-white text-xs font-medium">
                                                                        {{ strtoupper(substr(($request->user?->name ?? 'No User Assigned'), 0, 1)) }}{{ strtoupper(substr(explode(' ', ($request->user?->name ?? 'No User Assigned'))[1] ?? '', 0, 1)) }}
                                                                    </div>
                                                                @endif
                                                                <span>{{ $request->user->name }}</span>
                                                            </div>
                                                        </td>
                                                        <td class="px-4 py-2 text-center">
                                                            {{ $request->vl_credits_requested }}</td>
                                                        <td class="px-4 py-2 text-center">
                                                            {{ $request->sl_credits_requested }}</td>
                                                        <td class="px-4 py-2 text-center">
                                                            <span
                                                                style="font-family: 'Arial', sans-serif; font-weight: bold;">&#8369;</span>
                                                            {{ number_format($request->vl_monetize_credits, 2) }}
                                                        </td>
                                                        <td class="px-4 py-2 text-center">
                                                            <span
                                                                style="font-family: 'Arial', sans-serif; font-weight: bold;">&#8369;</span>
                                                            {{ number_format($request->sl_monetize_credits, 2) }}
                                                        </td>
                                                        <td class="px-4 py-2 text-center">
                                                            <span
                                                                class="inline-block px-3 py-1 text-xs font-semibold
                                                            {{ $request->status === 'Approved'
                                                                ? 'text-green-800 bg-green-200'
                                                                : ($request->status === 'Disapproved'
                                                                    ? 'text-red-800 bg-red-200'
                                                                    : ($request->status === 'Pending'
                                                                        ? 'text-yellow-800 bg-yellow-200'
                                                                        : '')) }} rounded-lg">
                                                                {{ $request->status }}
                                                            </span>
                                                        </td>
                                                        <td
                                                            class="px-5 py-4 text-sm font-medium text-right whitespace-nowrap sticky right-0 z-10 bg-white dark:bg-gray-800">
                                                            <button @click="$wire.confirmApprove({{ $request->id }})"
                                                                class="text-blue-500 {{ $request->status !== 'Pending' ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                                :disabled="{{ $request->status !== 'Pending' ? 'true' : 'false' }}">
                                                                <i class="bi bi-check-lg" title="Approve"></i>
                                                            </button>
                                                            <button @click="$wire.openDisapproveModal({{ $request->id }})"
                                                                class="text-red-500 {{ $request->status !== 'Pending' ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                                :disabled="{{ $request->status !== 'Pending' ? 'true' : 'false' }}">
                                                                <i class="bi bi-x" title="Disapprove"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="6" class="p-4 text-center text-sm text-gray-500 dark:text-gray-300">
                                                        No records!
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <div class="p-5 text-neutral-500 dark:text-neutral-200 bg-gray-200 dark:bg-gray-700">
                                    {{ $monetizationRequests->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <x-modal maxWidth="lg" wire:model="showConfirmModal" centered>
        <div class="p-6">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">
                    Are you sure you want to approve this request?
                </h3>
                <button wire:click="$set('showConfirmModal', false)"
                    class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 focus:outline-none">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <form class="space-y-6" wire:submit.prevent="approveRequest">
                <!-- Action Buttons -->
                <div class="mt-6 flex justify-end space-x-4">
                    <button type="submit"
                        class="px-4 py-2 rounded-md bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800">
                        Yes
                    </button>
                    <button type="button" wire:click="$set('showConfirmModal', false)"
                        class="px-4 py-2 rounded-md bg-gray-700 hover:bg-gray-800 text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:focus:ring-offset-gray-800">
                        No
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- Disapprove Modal -->
    <x-modal maxWidth="lg" wire:model="showDisapproveModal" centered>
        <div class="p-6">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">
                    Are you sure you want to disapprove this request?
                </h3>
                <button wire:click="$set('showDisapproveModal', false)"
                    class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 focus:outline-none">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <form class="space-y-6" wire:submit.prevent="disapproveRequest">
                <!-- Action Buttons -->
                <div class="mt-6 flex justify-end space-x-4">
                    <button type="submit"
                        class="px-4 py-2 rounded-md bg-red-500 hover:bg-red-600 text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:focus:ring-offset-gray-800">
                        Yes
                    </button>
                    <button type="button" wire:click="$set('showDisapproveModal', false)"
                        class="px-4 py-2 rounded-md bg-gray-700 hover:bg-gray-800 text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:focus:ring-offset-gray-800">
                        No
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</div>

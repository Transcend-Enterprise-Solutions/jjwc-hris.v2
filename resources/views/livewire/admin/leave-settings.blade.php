<div class="w-full">
    <div class="w-full bg-white rounded-2xl p-6 shadow dark:bg-gray-800">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Leave Approval Settings</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Configure leave approval workflow and assign approvers
                </p>
            </div>
        </div>

        <!-- Information Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- First Approver Card -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-800 rounded-lg flex items-center justify-center">
                        <i class="bi bi-person-check text-blue-600 dark:text-blue-400 text-lg"></i>
                    </div>
                    <h3 class="font-semibold text-blue-800 dark:text-blue-200">First Approver</h3>
                </div>
                <p class="text-sm text-blue-700 dark:text-blue-300 mb-2">
                    Supervisor (SV)
                </p>
                <div class="text-xs text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-800 px-2 py-1 rounded">
                    Initial approval stage
                </div>
            </div>

            <!-- Second Approver Card -->
            <div class="bg-violet-50 dark:bg-violet-900/20 border border-violet-200 dark:border-violet-800 rounded-xl p-6">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-violet-100 dark:bg-violet-800 rounded-lg flex items-center justify-center">
                        <i class="bi bi-person-gear text-violet-600 dark:text-violet-400 text-lg"></i>
                    </div>
                    <h3 class="font-semibold text-violet-800 dark:text-violet-200">Second Approver</h3>
                </div>
                <p class="text-sm text-violet-700 dark:text-violet-300 mb-2">
                    Supervisor (SV)
                </p>
                <div class="text-xs text-violet-600 dark:text-violet-400 bg-violet-100 dark:bg-violet-800/50 px-2 py-1 rounded">
                    Secondary approval stage
                </div>
            </div>

            <!-- Third Approver Card -->
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-6">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-green-100 dark:bg-green-800 rounded-lg flex items-center justify-center">
                        <i class="bi bi-person-badge text-green-600 dark:text-green-400 text-lg"></i>
                    </div>
                    <h3 class="font-semibold text-green-800 dark:text-green-200">Third Approver</h3>
                </div>
                <p class="text-sm text-green-700 dark:text-green-300 mb-2">
                    Human Resources (HR)
                </p>
                <div class="text-xs text-green-600 dark:text-green-400 bg-green-100 dark:bg-green-800 px-2 py-1 rounded">
                    Final approval stage
                </div>
            </div>
        </div>

        <!-- Approvers Table -->
        <div class="overflow-x-auto">
            <div class="overflow-hidden border dark:border-gray-700 rounded-lg">
                <div class="inline-block w-full align-middle">
                    <div class="overflow-hidden border dark:border-gray-700 rounded-lg text-xs">
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-full">
                                <thead class="bg-gray-200 dark:bg-gray-700 rounded-xl">
                                    <tr>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Approver Level
                                        </th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Required Role
                                        </th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Assigned User
                                        </th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            OIC
                                        </th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th
                                            class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($approvers as $approver)
                                        <tr class="">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="w-2 h-2 rounded-full @if ($approver->approver_level === 'first') bg-blue-500 @elseif($approver->approver_level === 'second') bg-violet-500 @else bg-green-500 @endif">
                                                    </div>
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $approverLevels[$approver->approver_level] }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium @if ($approver->approver_level === 'first') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 @elseif($approver->approver_level === 'second') bg-violet-100 text-violet-800 dark:bg-violet-900 dark:text-violet-200 @else bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @endif">
                                                    {{ strtoupper($approver->required_role) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($approver->user)
                                                    <div class="flex items-center gap-3">
                                                        @if ($approver->user->profile_photo_path)
                                                            <img src="{{ route('profile-photo.file', ['filename' => basename($approver->user->profile_photo_path)]) }}"
                                                                alt="{{ $approver->user->name }}"
                                                                class="w-8 h-8 rounded-full object-cover border border-gray-300 dark:border-gray-600">
                                                        @else
                                                            <div
                                                                class="w-8 h-8 rounded-full bg-gray-400 flex items-center justify-center text-white text-xs font-medium">
                                                                {{ strtoupper(substr($approver->user->name, 0, 1)) }}
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <div
                                                                class="text-sm font-medium text-gray-900 dark:text-white">
                                                                {{ $approver->user->name }}
                                                            </div>
                                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                                {{ strtoupper($approver->user->user_role) }} -
                                                                {{ $approver->user->emp_code ?? 'N/A' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-sm text-gray-500 dark:text-gray-400 italic">Not
                                                        assigned</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($approver->oicUser)
                                                    <div class="flex items-center gap-3">
                                                        @if ($approver->oicUser->profile_photo_path)
                                                            <img src="{{ route('profile-photo.file', ['filename' => basename($approver->oicUser->profile_photo_path)]) }}"
                                                                alt="{{ $approver->oicUser->name }}"
                                                                class="w-8 h-8 rounded-full object-cover border border-gray-300 dark:border-gray-600">
                                                        @else
                                                            <div
                                                                class="w-8 h-8 rounded-full bg-gray-400 flex items-center justify-center text-white text-xs font-medium">
                                                                {{ strtoupper(substr($approver->oicUser->name, 0, 1)) }}
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <div
                                                                class="text-sm font-medium text-gray-900 dark:text-white">
                                                                {{ $approver->oicUser->name }}
                                                            </div>
                                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                                {{ strtoupper($approver->oicUser->user_role) }} -
                                                                {{ $approver->oicUser->emp_code ?? 'N/A' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-sm text-gray-500 dark:text-gray-400 italic">No OIC
                                                        assigned</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $approver->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                                    {{ $approver->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex justify-end items-center gap-2">
                                                    {{-- Admin can edit approver assignment --}}
                                                    @if (auth()->user()->user_role === 'sa')
                                                        <button wire:click="openApproverModal({{ $approver->id }})"
                                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 transition-colors p-1 rounded"
                                                            title="Edit Approver">
                                                            <i class="bi bi-pencil text-sm"></i>
                                                        </button>
                                                    @endif

                                                    {{-- Only the assigned user can manage their OIC --}}
                                                    @if ($approver->user_id === auth()->id())
                                                        <button wire:click="openOICModal({{ $approver->id }})"
                                                            class="text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300 transition-colors p-1 rounded"
                                                            title="Assign My OIC">
                                                            <i class="bi bi-person-plus text-sm"></i>
                                                        </button>
                                                    @else
                                                        <button disabled
                                                            class="text-gray-400 dark:text-gray-600 cursor-not-allowed p-1 rounded opacity-50"
                                                            title="Only the assigned approver can set OIC">
                                                            <i class="bi bi-person-plus text-sm"></i>
                                                        </button>
                                                    @endif

                                                    {{-- Admin can toggle status --}}
                                                    @if (auth()->user()->user_role === 'sa')
                                                        <button wire:click="toggleApproverStatus({{ $approver->id }})"
                                                            class="text-orange-600 hover:text-orange-900 dark:text-orange-400 dark:hover:text-orange-300 transition-colors p-1 rounded"
                                                            title="{{ $approver->is_active ? 'Deactivate' : 'Activate' }}">
                                                            <i class="bi bi-power text-lg"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Approver Modal (Admin Only) -->
    <x-modal wire:model="showApproverModal" max-width="2xl" centered>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Edit Approver
                </h2>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="bi bi-x-lg text-xl"></i>
                </button>
            </div>

            <form wire:submit.prevent="saveApprover">
                <div class="space-y-6">
                    <!-- Approver Level (Read-only) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Approver Level
                        </label>
                        <input type="text" value="{{ $approverLevels[$approver_level] ?? '' }}" readonly
                            class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 text-gray-900 dark:text-white cursor-not-allowed">
                    </div>

                    <!-- Required Role (Read-only) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Required Role
                        </label>
                        <input type="text" value="{{ strtoupper($roleDisplayNames[$approver_level] ?? '') }}"
                            readonly
                            class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 text-gray-900 dark:text-white cursor-not-allowed">
                    </div>

                    <!-- Assigned User -->
                    @if ($approver_level)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Assign User *
                            </label>
                            <select wire:model="user_id" required
                                class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                <option value="">Select User</option>
                                @foreach ($this->getAvailableUsers($approver_level) as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->name }} ({{ strtoupper($user->user_role) }} -
                                        {{ $user->emp_code ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                </div>

                <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-600">
                    <button type="button" wire:click="closeModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                        Update Approver
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- OIC Modal (Only for assigned approvers) -->
    <x-modal wire:model="showOICModal" max-width="md" centered>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Assign My Officer-in-Charge
                </h2>
                <button wire:click="closeOICModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="bi bi-x-lg text-xl"></i>
                </button>
            </div>

            <div
                class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                <p class="text-sm text-blue-700 dark:text-blue-300">
                    <i class="bi bi-info-circle mr-2"></i>
                    Select an employee who will act as your Officer-in-Charge during your absence.
                </p>
            </div>

            <form wire:submit.prevent="saveOIC">
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Select OIC (Employee)
                        </label>
                        <select wire:model="oic_user_id"
                            class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                            <option value="">No OIC (I will handle approvals myself)</option>

                            {{-- Group by OIC status for better visibilityy --}}
                            @php
                                $oicUsers = $this->getAvailableOICUsers()->filter(fn($user) => $user->is_oic);
                                $nonOicUsers = $this->getAvailableOICUsers()->filter(fn($user) => !$user->is_oic);
                            @endphp

                            @if ($oicUsers->count() > 0)
                                <optgroup label="Current OICs">
                                    @foreach ($oicUsers as $user)
                                        <option value="{{ $user->id }}">
                                            {{ $user->name }} ({{ $user->emp_code ?? 'N/A' }}) - ✅ OIC
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif

                            @if ($nonOicUsers->count() > 0)
                                <optgroup label="Other Employees">
                                    @foreach ($nonOicUsers as $user)
                                        <option value="{{ $user->id }}">
                                            {{ $user->name }} ({{ $user->emp_code ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-600">
                    <button type="button" wire:click="closeOICModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-purple-600 border border-transparent rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-colors">
                        Assign OIC
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</div>